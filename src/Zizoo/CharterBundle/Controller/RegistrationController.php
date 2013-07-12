<?php

namespace Zizoo\CharterBundle\Controller;

use Zizoo\CharterBundle\Form\Type\CharterRegistrationType;
use Zizoo\CharterBundle\Entity\CharterRepository;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RegistrationController extends Controller
{
    private function doLogin($user, $url=null){
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
        $securityContext = $this->get('security.context');
        $securityContext->setToken($token);
        if ($url) return $this->redirect($url);
    }

    
    /**
     * Try to register a user.
     * 
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function registerAction()
    {
        $charterRegistrationType = $this->container->get('zizoo_charter.charter_registration_type');
        $form = $this->createForm($charterRegistrationType, null, array('map_drag'          => true, 
                                                                        'map_update'        => true,
                                                                        'validation_groups' => array('registration')));
        $request = $this->getRequest();
        $relogin = $request->query->get('relogin', false);
        $isPost = $request->isMethod('POST');
        $facebook = $this->get('facebook');
        
        // If submit
        if ($isPost) {
            $form->bind($request);

            $data = $form->getData();
            $charter    = $data->getCharter();
            $user       = $data->getRegistration()->getUser();
            $profile    = $data->getRegistration()->getProfile();
            $profileAddress = new \Zizoo\AddressBundle\Entity\ProfileAddress($charter->getAddress());
            $profile->setAddress($profileAddress);
            $profileAddress->setProfile($profile);
            
            if ($form->isValid()) {
                $charterService = $this->get('zizoo_charter_charter_service');
                $userService    = $this->get('zizoo_user_user_service');
                $messenger      = $this->get('messenger');
                
                $charterService->setupCharter($charter, $user, $user, false);
                $user = $userService->registerUser($user, $profile, true);
                $messenger->sendConfirmationEmail($user);

                return $this->redirect($this->generateUrl('ZizooCharterBundle_Registration_submitted'));
            } else {
                // Form is not valid. Check if the user is valid. If not, see if it's because the user already exists and hasn't completed registration (i.e. confirmation)
                // If that is the case, forward to "resend_confirmation". 
                // Maybe this shouldn't be done automatically? But it's hard to get the "Have you previously signed up" message below the right form control (i.e. username or email)
                $em = $this->getDoctrine()
                            ->getManager();
                
                // Validate user
                $validator = $this->get('validator');
                $errors = $validator->validate($user, 'registration');
                $num_errors = $errors->count();
                
                // See if invalid because user or email already taken.
                $possibleUnconfirmedUser = null;
                for ($i=0; $i<$num_errors; $i++){
                    $msgTemplate = $errors->get($i)->getMessageTemplate();
                    if ($msgTemplate=='zizoo_user.error.email_taken'){
                        $email = $errors->get($i)->getRoot()->getEmail();
                        if ($email){
                            $possibleUnconfirmedUser = $em->getRepository('ZizooUserBundle:User')->findOneByEmail($email);
                            // If email already taken and not yet confirmed, forward.
                            if ($possibleUnconfirmedUser->getConfirmationToken()!=null && !$possibleUnconfirmedUser->getIsActive()){
                                return $this->render('ZizooCharterBundle:Registration:register.html.twig', array('form' => $form->createView(), 
                                                                                                                'unconfirmed_user'      => $possibleUnconfirmedUser, 
                                                                                                                'unconfirmed_email'     => true, 
                                                                                                                'unconfirmed_username'  => false, 
                                                                                                                'is_post'               => $isPost, 
                                                                                                                'relogin'               => $relogin,
                                                                                                                'ajax'                  => $request->isXmlHttpRequest()));
                            }
                        }
                    } else if ($msgTemplate=='zizoo_user.error.user_taken'){
                        $username = $errors->get($i)->getRoot()->getUsername();
                        if ($username){
                            $possibleUnconfirmedUser = $em->getRepository('ZizooUserBundle:User')->findOneByUsername($username);
                            // If username already taken and not yet confirmed, forward.
                            if ($possibleUnconfirmedUser->getConfirmationToken()!=null && !$possibleUnconfirmedUser->getIsActive()){
                                return $this->render('ZizooCharterBundle:Registration:register.html.twig', array('form' => $form->createView(), 
                                                                                                                'unconfirmed_user'      => $possibleUnconfirmedUser, 
                                                                                                                'unconfirmed_email'     => false, 
                                                                                                                'unconfirmed_username'  => true, 
                                                                                                                'is_post'               => $isPost, 
                                                                                                                'relogin'               => $relogin,
                                                                                                                'ajax'                  => $request->isXmlHttpRequest()));
                            }
                        }
                    }
                }
                
            }
        }

        return $this->render('ZizooCharterBundle:Registration:register.html.twig', array('form' => $form->createView(), 
                                                                                        'unconfirmed_user'  => null, 
                                                                                        'unconfirmed_email'     => false, 
                                                                                        'unconfirmed_username'  => false, 
                                                                                        'facebook'              => $facebook, 
                                                                                        'is_post'               => $isPost, 
                                                                                        'relogin'               => $relogin,
                                                                                        'ajax'                  => $request->isXmlHttpRequest()));
    }
    
    /**
     * Registration form submitted successfully.
     * Redirected to from registerAction() (which also sends confirmation email, by calling sendConfirmationEmail()).
     * 
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function submittedAction(){
        $request = $this->getRequest();
        
        return $this->render('ZizooCharterBundle:Registration:submitted.html.twig', array( 'ajax' => $request->isXmlHttpRequest() ));
    }
    
    /**
     * Resend confirmation email.
     * @param string $email
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function resendConfirmationAction($email){
        $request = $this->getRequest();
        $user = null;
        $em = $this->getDoctrine()
                    ->getManager();
        if ($email!=null){
            $user = $em->getRepository('ZizooUserBundle:User')->findOneByEmail($email);
        } 
        if ($user==null || $user->getConfirmationToken()==null){
            return $this->redirect($this->generateUrl('ZizooCharterBundle_Registration_submitted'));
        }
        if ($request->isMethod('POST')) {
            $messenger = $this->get('messenger');
            $messenger->sendConfirmationEmail($user);
            return $this->redirect($this->generateUrl('ZizooCharterBundle_Registration_submitted'));
        }
        return $this->render('ZizooCharterBundle:Registration:resend_confirmation.html.twig');
    }
    
    /**
     * Confirm a registration by token and email. This is done by clicking on the link in the confirmation email.
     * 
     * @param string $token
     * @param string $email
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function confirmAction($token, $email){
        $userService = $this->get('zizoo_user_user_service');
        $user = $userService->confirmUser($token, $email);
        
        if ($user){
            $messenger = $this->container->get('messenger');        
            $message = $messenger->sendRegistrationEmail($user);
            
            return $this->doLogin($user, $this->generateUrl('ZizooCharterBundle_Registration_confirmed'));
        } else {
            return $this->redirect($this->generateUrl('ZizooCharterBundle_Registration_register'));
        }
        
    }
    
    /**
     * Registration confirmed.
     * 
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function confirmedAction(){
        $request = $this->getRequest();
        $user = $this->getUser();
        
        return $this->render('ZizooCharterBundle:Registration:confirmed.html.twig', array(  'user'    => $user,
                                                                                            'ajax'    => $request->isXmlHttpRequest() ));
    }
    
    
    
    
}
