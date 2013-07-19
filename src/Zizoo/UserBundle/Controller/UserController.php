<?php

// src/Zizoo/UserBundle/Controller/UserController.php;
namespace Zizoo\UserBundle\Controller;


use Zizoo\UserBundle\Form\Type\AccountSettingsType;
use Zizoo\UserBundle\Form\Model\AccountSettings;

use Zizoo\UserBundle\Entity\User;
use Zizoo\UserBundle\Form\Type\UserForgotPasswordType;
use Zizoo\UserBundle\Form\Type\InvitationType;
use Zizoo\UserBundle\Form\Model\Invitation;
use Zizoo\UserBundle\Form\Type\InviteType;
use Zizoo\UserBundle\Form\Model\Invite;
use Zizoo\UserBundle\Form\Type\InviteSingleType;
use Zizoo\UserBundle\Form\Model\InviteSingle;
use Zizoo\UserBundle\Form\Type\UserNewPasswordType;
use Zizoo\UserBundle\Form\Model\UserNewEmail;

use Zizoo\AddressBundle\Form\Model\SearchBoat;
use Zizoo\AddressBundle\Form\Type\SearchBoatType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserController extends Controller
{
    
    private function doLogin($user, $url=null){
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
        $securityContext = $this->get('security.context');
        $securityContext->setToken($token);
        if ($url) return $this->redirect($url);
    }
    
    /**
     * Login form.
     * 
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        $routeName = $request->get('_route');
        $facebook = $this->get('facebook');
        
        $unconfirmedUser    = null;
        if ($error instanceof \Symfony\Component\Security\Core\Exception\DisabledException){
            $lastUsername   = $session->get(SecurityContext::LAST_USERNAME);
            $userRepo       = $this->getDoctrine()->getManager()->getRepository('ZizooUserBundle:User');
            $emailUser      = $userRepo->findOneByEmail($lastUsername);
            $usernameUser   = $userRepo->findOneByUsername($lastUsername);
            if ($emailUser){
                $unconfirmedUser = $emailUser->getConfirmationToken()!=null?$emailUser:null;
            } else if ($usernameUser){
                $unconfirmedUser = $usernameUser->getConfirmationToken()!=null?$usernameUser:null;
            }
        }
        
        return $this->render('ZizooUserBundle:User:login.html.twig', array(
            // last username entered by the user
            'last_username'         => $session->get(SecurityContext::LAST_USERNAME),
            'error'                 => $error,
            'unconfirmed_user'      => $unconfirmedUser,
            'current_route'         => $routeName,
            'facebook'              => $facebook,
            'ajax'                  => $request->isXmlHttpRequest()
        ));
    }
    
    public function facebookLoginFailAction(){
        $request = $this->getRequest();
        
        $facebook = $this->get('facebook');
        
        return $this->render('ZizooUserBundle:User:login_facebook_fail.html.twig', array( 'ajax'        => $request->isXmlHttpRequest(),
                                                                                          'facebook'    => $facebook));
    }
        
    public function loginFacebookAction(){
        $request = $this->getRequest();
        $ajax = $request->query->get('ajax', false);
        
        $facebook = $this->get('facebook');
        try {
            $obj = $facebook->api('/me');
        } catch (FacebookApiException $e){
            return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => 'facebook',
                                                                                          'ajax'    => $ajax ));
        }
        
        if (!array_key_exists('id', $obj)){
            return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => 'ZizooUserBundle_login',
                                                                                          'ajax'    => $ajax  ));
        }
        
        $em = $this->getDoctrine()
                    ->getManager();
            
        $user = $em->getRepository('ZizooUserBundle:User')->findOneByFacebookUID($obj['id']);
        
        if ($user){
            $request        = $this->getRequest();
            $forward        =  $request->query->get('forward', null);
            if (!$forward) $forward = 'ZizooBaseBundle_Dashboard_UserDashboard';
            $this->doLogin($user);
            return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => $forward,
                                                                                          'ajax'    => $ajax  ));
        } else {
            return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => 'ZizooUserBundle_login_facebook_fail',
                                                                                          'ajax'    => $ajax  ));
        }
        
    }
    
    public function logoutFacebookAction(){
        $request = $this->getRequest();
        $ajax = $request->query->get('ajax', false);
        
        return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => 'ZizooUserBundle_login_facebook',
                                                                                      'ajax'    => $ajax  ));
    }
    
    /**
     * User forgot password. Set confirmation token and sendForgotPasswordEmail()
     * 
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function forgotPasswordAction(){
        $request = $this->getRequest();
        $form = $this->createForm(new UserForgotPasswordType());
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            $data = $form->getData();
            $user_or_email = $data['user_or_email'];
            
            $em = $this->getDoctrine()
                       ->getManager();
            
            $user = $em->getRepository('ZizooUserBundle:User')->findOneByUsername($user_or_email);
            if ($user==null) $user = $em->getRepository('ZizooUserBundle:User')->findOneByEmail($user_or_email);
            if ($user==null){
                return $this->render('ZizooUserBundle:User:forgot_password.html.twig', array(   'form'      => $form->createView(), 
                                                                                                'error'     => 'no_such_user',
                                                                                                'ajax'      => $request->isXmlHttpRequest() ));
            }
            
            $user->setConfirmationToken(uniqid());
            
            $em->persist($user);
            $em->flush();
            
            $messenger = $this->get('messenger');
            $messenger->sendForgotPasswordEmail($user);
            
            return $this->redirect($this->generateUrl('ZizooUserBundle_reset_password_email'));
        }
        
        return $this->render('ZizooUserBundle:User:forgot_password.html.twig', array(   'form'  => $form->createView(),
                                                                                        'ajax'  => $request->isXmlHttpRequest() ));
    }
    
    /**
     * Reset password email was sent. 
     * 
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function resetPasswordEmailAction(){
        $request = $this->getRequest();
        return $this->render('ZizooUserBundle:User:reset_password_email.html.twig', array( 'ajax' => $request->isXmlHttpRequest() ));
    }
    
    /**
     * Actually reset the password by token and email. This is done by clicking on the link in the reset password email.
     * 
     * @param string $token
     * @param string $email
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function resetPasswordAction($token, $email){
        $em = $this->getDoctrine()
                   ->getManager();
        
        $user = $em->getRepository('ZizooUserBundle:User')->findOneByEmail($email);
        
        if ($user && $user->getConfirmationToken()===$token){
            $user->setConfirmationToken(null);
            $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
            $pass_plain = uniqid();
            $password = $encoder->encodePassword($pass_plain, $user->getSalt());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();
            
            $messenger = $this->get('messenger');
            $messenger->sendNewPasswordEmail($user, $pass_plain);
            return $this->render('ZizooUserBundle:User:reset_password_confirm.html.twig');
        } else {
            return $this->redirect($this->generateUrl('ZizooUserBundle_login'));
        }
    }
    
    /**
     * Reset password confirmed.
     * 
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function resetPasswordConfirmAction(){
        return $this->render('ZizooUserBundle:Email:reset_password.html.twig');
    }
 
    
    public function inviteFriendsAction(){
        $user       = $this->getUser();
        $request    = $this->getRequest();
        $isPost     = $request->isMethod('POST');
        $ajax       = $request->isXmlHttpRequest();      
        $newInvite  = new Invite();
        $newInviteSingle = new InviteSingle();
        $newInviteSingle->setEmail('');
        $newInvite->addEmail($newInviteSingle);
        
        $form       = $this->createForm('zizoo_invite', $newInvite);
        
        // If submit
        if ($isPost) {
            $form->bind($request);
            
            $newInvite = $form->getData();
            
            if ($form->isValid()){
                $messenger  = $this->get('messenger');
                $trans      = $this->get('translator');
                $em         = $this->getDoctrine()
                                    ->getManager();         
                
                foreach ($newInvite->getEmails() as $singleInvite){
                    $messenger->sendInvitationEmail($singleInvite->getEmail(), $user);
                    $this->get('session')->getFlashBag()->add('notice', $singleInvite->getEmail() . ' ' . $trans->trans('zizoo_user.friends_invited'));
                }
                return $this->redirect($this->generateUrl('ZizooUserBundle_invite', array('form' => $form)));
            }
        }
        
        return $this->container->get('templating')->renderResponse('ZizooUserBundle:User:invite.html.twig', array(
            'form'  => $form->createView(),
            'ajax'  => $ajax
        ));
    }
    
    
    /**
     * Confirm change of email by token and email. This is done by clicking on the link in the confirmation email.
     * 
     * @param string $token
     * @param string $email
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function confirmChangeEmailAction($token, $email){
        $userService = $this->get('zizoo_user_user_service');
        $user = $userService->confirmChangeEmail($token, $email);
        
        if ($user){
            $trans = $this->get('translator');
            $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_user.message.email_changed_confirmed', array('%email%' => $user->getEmail())));      
            
            return $this->doLogin($user, $this->generateUrl('ZizooBaseBundle_Dashboard_AccountSettings'));
        } else {
            return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard_AccountSettings'));
        }
        
    }
    
    /**
     * Edit Account Settings: change email address and/or change password.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return Response
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function accountSettingsAction(Request $request){
        $user = $this->getUser();
        $form = $this->createForm(new AccountSettingsType(), null, array('label' => false));
        
        // If submit
        if ($request->isMethod('POST')) {
            $form->bind($request);
            $trans = $this->get('translator');
            
            $accountSettings = $form->getData();
            if ($form->isValid()) {
                
                $newEmail = $accountSettings->getNewEmail();
                if ($newEmail!=null && $newEmail!=$user->getEmail()){

                    $existingUser = $this->getDoctrine()->getRepository('ZizooUserBundle:User')->findOneByEmail($newEmail);
                    if ($existingUser){
                        $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_user.error.email_taken'));
                        return $this->redirect($this->generateUrl($request->query->get('redirect_route')));
                    }

                    $userService    = $this->get('zizoo_user_user_service');
                    $messenger      = $this->get('messenger');
                    $userService->changeEmail($user, $newEmail);
                    $messenger->sendChangeEmailConfirmationEmail($user);
                    $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_user.message.email_changed', array('%email%' => $newEmail)));
                }

                $newPassword = $accountSettings->getNewPassword();
                if ($newPassword->getPassword()!=null){

                    $user->setPassword($encoder->encodePassword($newPassword->getPassword(), $user->getSalt()));

                    $em = $this->getDoctrine()
                               ->getManager();
                    $em->persist($user);
                    $em->flush();


                    $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_user.message.password_changed'));


                }

                return $this->redirect($this->generateUrl($request->query->get('redirect_route')));

            }
   
        } else {
            $newEmail = new UserNewEmail();
            $newEmail->setEmail($user->getEmail());
            $accountSettings = new AccountSettings();
            $accountSettings->setNewEmail($newEmail);
            $form = $this->createForm(new AccountSettingsType(), $accountSettings);
        }

        $searchForm = $this->createForm(new SearchBoatType($this->container), new SearchBoat());
        
        return $this->render('ZizooUserBundle:User:account_settings.html.twig',
            array(
                'form' => $form->createView(),
                'searchForm' => $searchForm->createView()
            )
        );
    }
   
}

?>
