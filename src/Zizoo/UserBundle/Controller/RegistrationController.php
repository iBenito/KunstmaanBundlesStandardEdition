<?php

namespace Zizoo\UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Zizoo\UserBundle\Form\Type\FacebookNewRegistrationType;
use Zizoo\UserBundle\Form\Type\FacebookNewUserType;
use Zizoo\UserBundle\Form\Type\RegistrationType;
use Zizoo\UserBundle\Form\Type\UserType;
use Zizoo\UserBundle\Form\Type\UserForgotPasswordType;
use Zizoo\UserBundle\Form\Model\Registration;
use Zizoo\UserBundle\Form\Model\ForgotPassword;

use Zizoo\UserBundle\Entity\User;
use Zizoo\UserBundle\Entity\Group;
use Zizoo\UserBundle\Service\FacebookApiException;

use Zizoo\ProfileBundle\Entity\Profile;

class RegistrationController extends Controller
{
    /**
     * Send email to registration user, with activation link
     * 
     * @param Zizoo\UserBundle\Entity\User $user
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    private function sendConfirmationEmail($user){
        $activationLink = $this->generateUrl('confirm', array('token' => $user->getConfirmationToken(), 'email' => $user->getEmail()), true);
        $twig = $this->container->get('twig');
        $template = $twig->loadTemplate('ZizooUserBundle:Registration:email_confirm.html.twig');
        $context = array('link' => $activationLink);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->container->getParameter('email_register'))
            ->setTo($user->getEmail());

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $this->get('mailer')->send($message);
    }
    
    
    private function doLogin($user, $url){
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
        $securityContext = $this->get('security.context');
        $securityContext->setToken($token);
        return $this->redirect($url);
    }

    
    /**
     * Try to register a user.
     * 
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function registerAction()
    {
        $form = $this->createForm(new RegistrationType());
        $request = $this->getRequest();
        $relogin = $request->query->get('relogin', false);
        $isPost = $request->isMethod('POST');
        $facebook = $this->get('facebook');
        
        // If submit
        if ($isPost) {
            $form->bindRequest($request);

            $data = $form->getData();
            $user = $data->getUser();
            if ($form->isValid()) {
                
                $user->setSalt(md5(time()));
                $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
                $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($password);
                $user->setConfirmationToken(uniqid());
                $em = $this->getDoctrine()
                           ->getEntityManager();
                
                $zizooUserGroup = $em->getRepository('ZizooUserBundle:Group')->findOneByRole('ROLE_ZIZOO_USER');
                
                $user->addGroup($zizooUserGroup);
                                
                $em->persist($zizooUserGroup);
                $em->persist($user);
                $em->flush();

                $this->sendConfirmationEmail($user);

                return $this->redirect($this->generateUrl('submitted'));
            } else {
                // Form is not valid. Check if the user is valid. If not, see if it's because the user already exists and hasn't completed registration (i.e. confirmation)
                // If that is the case, forward to "resend_confirmation". 
                // Maybe this shouldn't be done automatically? But it's hard to get the "Have you previously signed up" message below the right form control (i.e. username or email)
                $em = $this->getDoctrine()
                            ->getEntityManager();
                
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
                                return $this->render('ZizooUserBundle:Registration:register.html.twig', array('form' => $form->createView(), 'unconfirmed_user' => $possibleUnconfirmedUser, 'unconfirmed_email' => true, 'unconfirmed_username' => false, 'facebook' => $facebook, 'is_post' => $isPost, 'relogin' => $relogin));
                                //return $this->redirect($this->generateUrl('resend-confirmation', array('email' => $email)));
                            }
                        }
                    } else if ($msgTemplate=='zizoo_user.error.user_taken'){
                        $username = $errors->get($i)->getRoot()->getUsername();
                        if ($username){
                            $possibleUnconfirmedUser = $em->getRepository('ZizooUserBundle:User')->findOneByUsername($username);
                            // If username already taken and not yet confirmed, forward.
                            if ($possibleUnconfirmedUser->getConfirmationToken()!=null && !$possibleUnconfirmedUser->getIsActive()){
                                return $this->render('ZizooUserBundle:Registration:register.html.twig', array('form' => $form->createView(), 'unconfirmed_user' => $possibleUnconfirmedUser, 'unconfirmed_email' => false, 'unconfirmed_username' => true, 'facebook' => $facebook, 'is_post' => $isPost, 'relogin' => $relogin));
                                //return $this->redirect($this->generateUrl('resend_confirmation', array('email' => $possibleUnconfirmedUser->getEmail())));
                            }
                        }
                    }
                }
                
            }
        }
        $router = $this->get('router');
        $fbAppId    = $this->container->getParameter('zizoo_user.facebook.app_id');
        
        return $this->render('ZizooUserBundle:Registration:register.html.twig', array('form' => $form->createView(), 'unconfirmed_user' => null, 'unconfirmed_email' => false, 'unconfirmed_username' => false, 'facebook' => $facebook, 'is_post' => $isPost, 'relogin' => $relogin));
    }
    
    /**
     * Registration form submitted successfully.
     * Redirected to from registerAction() (which also sends confirmation email, by calling sendConfirmationEmail()).
     * 
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function submittedAction(){
        return $this->render('ZizooUserBundle:Registration:submitted.html.twig');
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
                    ->getEntityManager();
        if ($email!=null){
            $user = $em->getRepository('ZizooUserBundle:User')->findOneByEmail($email);
        } 
        if ($user==null || $user->getConfirmationToken()==null){
            return $this->redirect($this->generateUrl('submitted'));
        }
        if ($request->isMethod('POST')) {
            $this->sendConfirmationEmail($user);
            return $this->redirect($this->generateUrl('submitted'));
        }
        return $this->render('ZizooUserBundle:Registration:resend_confirmation.html.twig');
    }
    
    /**
     * Confirm a registration by token and email. This is done by clicking on the link in the confirmation email.
     * 
     * @param string $token
     * @param string $email
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function confirmAction($token, $email){
        $em = $this->getDoctrine()
                   ->getEntityManager();
        
        $user = $em->getRepository('ZizooUserBundle:User')->findOneByEmail($email);
        
        if ($user && $user->getConfirmationToken()===$token){
            $user->setConfirmationToken(null);
            $user->setIsActive(1);
            
            $profile = new Profile();
            $profile->setCreated(new \DateTime());
            $profile->setUpdated($profile->getCreated());
            $profile->setUser($user);
            
            $em->persist($profile);
            $em->persist($user);
            $em->flush();
            
            return $this->doLogin($user, $this->generateUrl('confirmed'));
            //return $this->redirect($this->generateUrl('confirmed'));
        } else {
            return $this->redirect($this->generateUrl('register'));
        }
        
    }
    
    /**
     * Registration confirmed.
     * 
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function confirmedAction(){
        $user = $this->getUser();
        return $this->render('ZizooUserBundle:Registration:confirmed.html.twig', array('user' => $user));
    }
    
    
    public function facebookMergedAction(){
        $user = $this->getUser();
        return $this->render('ZizooUserBundle:Registration:facebook_merged.html.twig', array('user' => $user));
    }
    
    
    private function parse_signed_request($signed_request, $secret) {
 
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);

        // decode the data
        $sig = base64_decode(strtr($encoded_sig, '-_', '+/'));

        $data = json_decode(base64_decode((strtr($payload, '-_', '+/'))), true);


        if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
          //echo 'Unknown algorithm. Expected HMAC-SHA256';
          return null;
        }

        // check sig
        $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($sig !== $expected_sig) {
          //print_r('Bad Signed JSON signature.');
          return null;
        }

        return $data;
    }
    
    public function registerFacebookNewAction(){
        $request = $this->getRequest();
        $isPost = $request->isMethod('POST');
        
        
        $facebook = $this->get('facebook');
        try {
            $obj = $facebook->api('/me');
        } catch (FacebookApiException $e){
            return $this->render('ZizooUserBundle:Registration:register_facebook_forward.html.twig', array( 'action' => 'register' ));
        }
        
        if (!array_key_exists('id', $obj)){
            return $this->redirect($this->generateUrl('register'));
        }
        
        if ($isPost) {
            $form = $this->createForm(new FacebookNewRegistrationType());
            $form->bindRequest($request);

            $data = $form->getData();
            $user = $data->getUser();
            if ($user->getFacebookUID()==$obj['id'] && $user->getEmail()==$obj['email']){
                if ($form->isValid()) {

                    $user->setSalt(md5(time()));
                    $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
                    $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                    $user->setPassword($password);
                    $user->setIsActive(1);
                    $em = $this->getDoctrine()
                               ->getEntityManager();

                    $zizooUserGroup = $em->getRepository('ZizooUserBundle:Group')->findOneByRole('ROLE_ZIZOO_USER');

                    $user->addGroup($zizooUserGroup);

                    $profile = new Profile();
                    $profile->setFirstName($obj['first_name']);
                    $profile->setLastName($obj['last_name']);
                    $profile->setCreated(new \DateTime());
                    $profile->setUpdated($profile->getCreated());
                    $profile->setUser($user);
                    
                    $em->persist($zizooUserGroup);
                    $em->persist($profile);
                    $em->persist($user);
                    $em->flush();

                    return $this->doLogin($user, $this->generateUrl('confirmed'));
                    //return $this->redirect($this->generateUrl('submitted'));
                } else {
                    // Form is not valid. Check if the user is valid. If not, see if it's because the user already exists and hasn't completed registration (i.e. confirmation)
                    // If that is the case, forward to "resend_confirmation". 
                    // Maybe this shouldn't be done automatically? But it's hard to get the "Have you previously signed up" message below the right form control (i.e. username or email)
                    $em = $this->getDoctrine()
                                ->getEntityManager();

                    // Validate user
                    $validator = $this->get('validator');
                    $errors = $validator->validate($user, 'registration');
                    $num_errors = $errors->count();

                    // See if invalid because user already taken.
                    $possibleUnconfirmedUser = null;
                    for ($i=0; $i<$num_errors; $i++){
                        $msgTemplate = $errors->get($i)->getMessageTemplate();
                        if ($msgTemplate=='zizoo_user.error.user_taken'){
                            $username = $errors->get($i)->getRoot()->getUsername();
                            if ($username){
                                $possibleUnconfirmedUser = $em->getRepository('ZizooUserBundle:User')->findOneByUsername($username);
                                // If username already taken and not yet confirmed, forward.
                                if ($possibleUnconfirmedUser->getConfirmationToken()!=null && !$possibleUnconfirmedUser->getIsActive()){
                                    return $this->render('ZizooUserBundle:Registration:register_facebook_new.html.twig', array('form' => $form->createView(), 'facebook' => $facebook, 'data' => $obj, 'unconfirmed_user' => $possibleUnconfirmedUser, 'unconfirmed_email' => false, 'unconfirmed_username' => true));
                                }
                            }
                        }
                    }

                }
                return $this->render('ZizooUserBundle:Registration:register_facebook_new.html.twig', array('form' => $form->createView(), 'facebook' => $facebook, 'data' => $obj, 'unconfirmed_user' => null, 'unconfirmed_email' => false, 'unconfirmed_username' => false));
            } else {
                return $this->redirect($this->generateUrl('register', array('relogin' => true)));
            }
        }
        $user = new User();
      
        if (array_key_exists('username', $obj)){
            $username = $obj['username'];
        } else {
            $username = preg_replace('/\s+/', '', $obj['name']);
        }
        $user->setUsername($username);
        $user->setEmail($obj['email']);
        $user->setFacebookUID($obj['id']);
        $pass_plain = uniqid();
        $user->setPassword($pass_plain);
        $registration = new Registration();
        $registration->setUser($user);
        $form = $this->createForm(new FacebookNewRegistrationType(), $registration);
        return $this->render('ZizooUserBundle:Registration:register_facebook_new.html.twig', array('form' => $form->createView(), 'facebook' => $facebook, 'data' => $obj, 'unconfirmed_user' => null, 'unconfirmed_email' => false, 'unconfirmed_username' => false));
    }
    
    public function registerFacebookLinkAction(){
        $request = $this->getRequest();
        $isPost = $request->isMethod('POST');
        
        $facebook = $this->get('facebook');
        try {
            $obj = $facebook->api('/me');
        } catch (FacebookApiException $e){
            return $this->render('ZizooUserBundle:Registration:register_facebook_forward.html.twig', array( 'action' => 'register' ));
        }
        
        if (!array_key_exists('id', $obj)){
            return $this->redirect($this->generateUrl('register'));
        }
        
        $em = $this->getDoctrine()
                   ->getEntityManager();
        
        $existingUser = $em->getRepository('ZizooUserBundle:User')->findOneByEmail($obj['email']);
        if (!$existingUser){
            return $this->redirect($this->generateUrl('register'));
        }
        
        $existingUser->setFacebookUID($obj['id']);
        $existingUser->setIsAtive(1);
        $existingUser->setConfirmationToken(null);
        $em->persist($existingUser);
        $em->flush();

        return $this->doLogin($existingUser, $this->generateUrl('register_facebook_merged'));
    }
    
    /**
     * 
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function registerFacebookAction(){
        
        $request = $this->getRequest();
        
        $em = $this->getDoctrine()
                   ->getEntityManager();
        
       
        
        $facebook = $this->get('facebook');
        try {
            $obj = $facebook->api('/me');
        } catch (FacebookApiException $e){
            return $this->render('ZizooUserBundle:Registration:register_facebook_forward.html.twig', array( 'action' => 'register' ));
        }

        if (!array_key_exists('id', $obj)){
            return $this->redirect($this->generateUrl('register'));
        }
        
        $alreadyExistsUser = $em->getRepository('ZizooUserBundle:User')->findOneByEmail($obj['id']);  
        if ($alreadyExistsUser){
            // User with Facebook UID already exists - log in
            die('Already exists!');
        }

        $possibleLinkUser = $em->getRepository('ZizooUserBundle:User')->findOneByEmail($obj['email']);
        if ($possibleLinkUser){
            return $this->render('ZizooUserBundle:Registration:register_facebook_forward.html.twig', array( 'action' => 'register_facebook_link' ));
        }
        
        return $this->render('ZizooUserBundle:Registration:register_facebook_forward.html.twig', array( 'action' => 'register_facebook_new' ));

        
        /**
        $fbRequest = $request->request->get('signed_request', null);
        $fbSecret = $this->container->getParameter('zizoo_user.facebook.app_secret');
        $response = $this->parse_signed_request($fbRequest, $fbSecret);
        
        if ($response==null){
            die('Error');
        }
        
        
        $user = new User();
        $user->setEmail($response['registration']['email']);
        $user->setUsername($response['registration']['username']);
        $user->setPassword($response['registration']['password']);
        $user->setSalt(md5(time()));
        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($password);
        $user->setConfirmationToken(null);
        $user->setIsActive(1);
        
        $zizooUserGroup = $em->getRepository('ZizooUserBundle:Group')->findOneByRole('ROLE_ZIZOO_USER');

        $user->addGroup($zizooUserGroup);

        $em->persist($zizooUserGroup);
        $em->persist($user);
        $em->flush();
        
         *
         */
        
    }
}
