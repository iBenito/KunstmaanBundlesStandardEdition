<?php

namespace Zizoo\UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Zizoo\UserBundle\Form\Type\FacebookNewRegistrationType;
use Zizoo\UserBundle\Form\Type\FacebookNewUserType;
use Zizoo\UserBundle\Form\Type\FacebookLinkRegistrationType;
use Zizoo\UserBundle\Form\Type\FacebookLinkUserType;
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
        $form = $this->createForm(new RegistrationType());
        $request = $this->getRequest();
        $relogin = $request->query->get('relogin', false);
        $isPost = $request->isMethod('POST');
        $facebook = $this->get('facebook');
        
        // If submit
        if ($isPost) {
            $form->bind($request);

            $data = $form->getData();
            $user       = $data->getUser();
            $profile    = $data->getProfile();
            if ($form->isValid()) {
                $userService    = $this->get('zizoo_user_user_service');
                $messenger      = $this->get('messenger');
                
                $user = $userService->registerUser($user, $profile);
                $messenger->sendConfirmationEmail($user);

                return $this->redirect($this->generateUrl('ZizooUserBundle_submitted'));
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
                                return $this->render('ZizooUserBundle:Registration:register.html.twig', array('form' => $form->createView(), 
                                                                                                                'unconfirmed_user'      => $possibleUnconfirmedUser, 
                                                                                                                'unconfirmed_email'     => true, 
                                                                                                                'unconfirmed_username'  => false, 
                                                                                                                'facebook'              => $facebook, 
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
                                return $this->render('ZizooUserBundle:Registration:register.html.twig', array('form' => $form->createView(), 
                                                                                                                'unconfirmed_user'      => $possibleUnconfirmedUser, 
                                                                                                                'unconfirmed_email'     => false, 
                                                                                                                'unconfirmed_username'  => true, 
                                                                                                                'facebook'              => $facebook, 
                                                                                                                'is_post'               => $isPost, 
                                                                                                                'relogin'               => $relogin,
                                                                                                                'ajax'                  => $request->isXmlHttpRequest()));
                            }
                        }
                    }
                }
                
            }
        }

        return $this->render('ZizooUserBundle:Registration:register.html.twig', array('form' => $form->createView(), 
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
        
        return $this->render('ZizooUserBundle:Registration:submitted.html.twig', array( 'ajax' => $request->isXmlHttpRequest() ));
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
            return $this->redirect($this->generateUrl('ZizooUserBundle_submitted'));
        }
        if ($request->isMethod('POST')) {
            $messenger = $this->get('messenger');
            $messenger->sendConfirmationEmail($user);
            return $this->redirect($this->generateUrl('ZizooUserBundle_submitted'));
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
        $userService = $this->get('zizoo_user_user_service');
        $user = $userService->confirmUser($token, $email);
        
        if ($user){
            $messenger = $this->container->get('messenger');        
            $message = $messenger->sendRegistrationEmail($user);
            
            return $this->doLogin($user, $this->generateUrl('ZizooUserBundle_confirmed'));
        } else {
            return $this->redirect($this->generateUrl('ZizooUserBundle_register'));
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
        
        return $this->render('ZizooUserBundle:Registration:confirmed.html.twig', array( 'user'    => $user,
                                                                                        'ajax'    => $request->isXmlHttpRequest() ));
    }
    
    public function facebookSuccessAction(){
        $request = $this->getRequest();
        $user = $this->getUser();
        
        $fbID   = null;
        $fbName = null;
        $facebook = $this->get('facebook');
        try {
            $obj = $facebook->api('/me');
            if (array_key_exists('id', $obj))   $fbID   = $obj['id'];
            if (array_key_exists('name', $obj)) $fbName = $obj['name'];
        } catch (FacebookApiException $e){
        }

        return $this->render('ZizooUserBundle:Registration:facebook_success.html.twig', array( 'user'    => $user,
                                                                                              'ajax'    => $request->isXmlHttpRequest(),
                                                                                              'fb_id'   => $fbID,
                                                                                              'fb_name' => $fbName));
    }
    
    public function facebookMergedAction(){
        $request = $this->getRequest();
        $user = $this->getUser();
        
        $fbID   = null;
        $fbName = null;
        $facebook = $this->get('facebook');
        try {
            $obj = $facebook->api('/me');
            if (array_key_exists('id', $obj))   $fbID   = $obj['id'];
            if (array_key_exists('name', $obj)) $fbName = $obj['name'];
        } catch (FacebookApiException $e){
        }

        return $this->render('ZizooUserBundle:Registration:facebook_merged.html.twig', array( 'user'    => $user,
                                                                                              'ajax'    => $request->isXmlHttpRequest(),
                                                                                              'fb_id'   => $fbID,
                                                                                              'fb_name' => $fbName));
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
            return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => 'ZizooUserBundle_register',
                                                                                          'ajax'    => $request->isXmlHttpRequest() ));
        }
        
        if (!array_key_exists('id', $obj)){
            return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => 'ZizooUserBundle_register',
                                                                                          'ajax'    => $request->isXmlHttpRequest() ));
        }
        
        if ($isPost) {
            
            $form = $this->createForm(new FacebookNewRegistrationType());
            $form->bind($request);
            
            $data = $form->getData();
            $user = $data->getUser();
            
            if ($form->isValid()){
                $userService    = $this->get('zizoo_user_user_service');
                $messenger      = $this->get('messenger');
                
                $user = $userService->registerFacebookUser($user, $obj);
                if ($user){
                    $messenger->sendRegistrationEmail($user);
                    return $this->doLogin($user, $this->generateUrl('ZizooUserBundle_register_facebook_success'));
                } else {
                    return $this->redirect($this->generateUrl('ZizooUserBundle_register', array('relogin' => true,
                                                                                'ajax'    => $request->isXmlHttpRequest())));
                }
                
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
                                return $this->render('ZizooUserBundle:Registration:register_facebook_new.html.twig', array('form'                   => $form->createView(), 
                                                                                                                            'facebook'              => $facebook, 
                                                                                                                            'data'                  => $obj, 
                                                                                                                            'unconfirmed_user'      => $possibleUnconfirmedUser, 
                                                                                                                            'unconfirmed_email'     => false, 
                                                                                                                            'unconfirmed_username'  => true,
                                                                                                                            'ajax'                  => $request->isXmlHttpRequest()));
                            }
                        }
                    }
                }
                
                return $this->render('ZizooUserBundle:Registration:register_facebook_new.html.twig', array('form'                   => $form->createView(), 
                                                                                                    'facebook'              => $facebook, 
                                                                                                    'data'                  => $obj, 
                                                                                                    'unconfirmed_user'      => null, 
                                                                                                    'unconfirmed_email'     => false, 
                                                                                                    'unconfirmed_username'  => false,
                                                                                                    'ajax'                  => $request->isXmlHttpRequest()));
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
        return $this->render('ZizooUserBundle:Registration:register_facebook_new.html.twig', array('form'                   => $form->createView(), 
                                                                                                    'facebook'              => $facebook, 
                                                                                                    'data'                  => $obj, 
                                                                                                    'unconfirmed_user'      => null, 
                                                                                                    'unconfirmed_email'     => false, 
                                                                                                    'unconfirmed_username'  => false,
                                                                                                    'ajax'                  => $request->isXmlHttpRequest()));
    }
    
    public function registerFacebookLinkAction(){
        $request = $this->getRequest();
        $isPost = $request->isMethod('POST');
        
        $facebook = $this->get('facebook');
        try {
            $obj = $facebook->api('/me');
        } catch (FacebookApiException $e){
            return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => 'ZizooUserBundle_register',
                                                                                          'ajax'    => $request->isXmlHttpRequest() ));
        }
        
        if (!array_key_exists('id', $obj)){
            return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => 'ZizooUserBundle_register',
                                                                                          'ajax'    => $request->isXmlHttpRequest() ));
        }
        
        
        
        $em = $this->getDoctrine()
                   ->getManager();
        
        $existingUser = $em->getRepository('ZizooUserBundle:User')->findOneByEmail($obj['email']);
        if (!$existingUser){
            return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => 'ZizooUserBundle_register',
                                                                                          'ajax'    => $request->isXmlHttpRequest() ));
        }
        
        $user = new User();
      
        if (array_key_exists('username', $obj)){
            $username = $obj['username'];
        } else {
            $username = preg_replace('/\s+/', '', $obj['name']);
        }
        $user->setUsername($existingUser->getUsername());
        $user->setEmail($obj['email']);
        $user->setFacebookUID($obj['id']);
        $pass_plain = uniqid();
        $user->setPassword($pass_plain);
        $registration = new Registration();
        $registration->setUser($user);
        $form = $this->createForm(new FacebookLinkRegistrationType(), $registration);
        
        
        if ($isPost) {        
            
            $form = $this->createForm(new FacebookLinkRegistrationType());
            $form->bind($request);
            $data = $form->getData();
            $linkUser = $data->getUser();
            
            if ($form->isValid()){
                $userService = $this->get('zizoo_user_user_service');
                $user = $userService->linkFacebookUser($linkUser, $obj);
                
                if ($user){
                    return $this->doLogin($existingUser, $this->generateUrl('ZizooUserBundle_register_facebook_merged'));
                } else {
                    return $this->redirect($this->generateUrl('ZizooUserBundle_register', array('relogin' => true,
                                                                                'ajax'    => $request->isXmlHttpRequest())));
                }
            } else {
                // Form is not valid. Check if the user is valid. If not, see if it's because the user already exists and hasn't completed registration (i.e. confirmation)
                // If that is the case, forward to "resend_confirmation". 
                // Maybe this shouldn't be done automatically? But it's hard to get the "Have you previously signed up" message below the right form control (i.e. username or email)
                $em = $this->getDoctrine()
                            ->getManager();

                // Validate user
                $validator = $this->get('validator');
                $errors = $validator->validate($existingUser, 'fb_link');
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
                                return $this->render('ZizooUserBundle:Registration:register_facebook_link.html.twig', array('form' => $form->createView(), 'facebook' => $facebook, 'data' => $obj, 'unconfirmed_user' => $possibleUnconfirmedUser, 'unconfirmed_email' => false, 'unconfirmed_username' => true));
                            }
                        }
                    }
                }
            }
        }
        
        return $this->render('ZizooUserBundle:Registration:register_facebook_link.html.twig', array('form' => $form->createView(), 
                                                                                                    'facebook' => $facebook, 
                                                                                                    'data' => $obj, 
                                                                                                    'unconfirmed_user' => null, 
                                                                                                    'unconfirmed_email' => false, 
                                                                                                    'unconfirmed_username' => false,
                                                                                                    'ajax'    => $request->isXmlHttpRequest()));
        
    }
    
    /**
     * 
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function registerFacebookAction(){
        
        $request = $this->getRequest();
        $ajax = $request->query->get('ajax', false);
        
        $em = $this->getDoctrine()
                   ->getManager();
        
       
        
        $facebook = $this->get('facebook');
        try {
            $obj = $facebook->api('/me');
        } catch (FacebookApiException $e){
            return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => 'ZizooUserBundle_register',
                                                                                          'ajax'    => $ajax));
        }

        if (!array_key_exists('id', $obj)){
            return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => 'ZizooUserBundle_register',
                                                                                          'ajax'    => $ajax));
        }
        
        $loggedInUser = $this->getUser();
        $alreadyExistsUser = $em->getRepository('ZizooUserBundle:User')->findOneByFacebookUID($obj['id']);  
        if (!$loggedInUser && $alreadyExistsUser){
            // User with Facebook UID already exists - log in
            if ($ajax){
                $this->doLogin($alreadyExistsUser);
                return $this->render('ZizooUserBundle:Registration:forward.html.twig', array(   'action' => 'ZizooBaseBundle_Dashboard',
                                                                                                'ajax'    => $ajax));
            } else {
                return $this->doLogin($alreadyExistsUser, $this->generateUrl('ZizooUserBundle_forward', array('action'  => 'ZizooBaseBundle_Dashboard',
                                                                                              'ajax'    => $ajax)));
            }
        } else if ($loggedInUser && $alreadyExistsUser && $loggedInUser->getID() == $alreadyExistsUser->getID()){
            // User with Facebook UID already exists and is already logged in - idiot!
            return $this->render('ZizooUserBundle:Registration:forward.html.twig', array('action' => 'ZizooBaseBundle_Dashboard',
                                                                                         'ajax'    => $ajax));
        } else if ($loggedInUser && $alreadyExistsUser && $loggedInUser->getID() != $alreadyExistsUser->getID()){
            // User with Facebook UID already exists but another user is already logged in - what to do?
            die('Please log out first');
        }

        $possibleLinkUser = $em->getRepository('ZizooUserBundle:User')->findOneByEmail($obj['email']);
        if ($possibleLinkUser){
            return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => 'ZizooUserBundle_register_facebook_link',
                                                                                          'ajax'    => $ajax));
        }
        
        return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action' => 'ZizooUserBundle_register_facebook_new',
                                                                                      'ajax'    => $ajax));

    }
    
    public function forwardAction($action){
        $request = $this->getRequest();
        
        return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action' => $action,
                                                                                      'ajax'    => $request->isXmlHttpRequest()));
    }
}
