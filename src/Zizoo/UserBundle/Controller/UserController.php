<?php

// src/Zizoo/UserBundle/Controller/UserController.php;
namespace Zizoo\UserBundle\Controller;

use Zizoo\UserBundle\Entity\User;
use Zizoo\UserBundle\Form\Type\UserForgotPasswordType;
use Zizoo\UserBundle\Form\Type\UserNewPasswordType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserController extends Controller
{
    
    private function doLogin($user){
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
        $securityContext = $this->get('security.context');
        $securityContext->setToken($token);
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
        
        return $this->render('ZizooUserBundle:User:login.html.twig', array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
            'current_route' => $routeName,
            'facebook'      => $facebook,
            'ajax'          => $request->isXmlHttpRequest()
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
            return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => 'login',
                                                                                          'ajax'    => $ajax  ));
        }
        
        $em = $this->getDoctrine()
                    ->getEntityManager();
            
        $user = $em->getRepository('ZizooUserBundle:User')->findOneByFacebookUID($obj['id']);
        
        if ($user){
            $request        = $this->getRequest();
            $forward        =  $request->query->get('forward', null);
            if (!$forward) $forward = 'ZizooBaseBundle_dashboard';
            $this->doLogin($user);
            return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => $forward,
                                                                                          'ajax'    => $ajax  ));
        } else {
            return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => 'login_facebook_fail',
                                                                                          'ajax'    => $ajax  ));
        }
        
    }
    
    public function logoutFacebookAction(){
        $request = $this->getRequest();
        $ajax = $request->query->get('ajax', false);
        
        return $this->render('ZizooUserBundle:Registration:forward.html.twig', array( 'action'  => 'login_facebook',
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
            $form->bindRequest($request);
            $data = $form->getData();
            $user_or_email = $data['user_or_email'];
            
            $em = $this->getDoctrine()
                       ->getEntityManager();
            
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

            $this->sendForgotPasswordEmail($user);
            
            return $this->redirect($this->generateUrl('reset_password_email'));
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
                   ->getEntityManager();
        
        $user = $em->getRepository('ZizooUserBundle:User')->findOneByEmail($email);
        
        if ($user && $user->getConfirmationToken()===$token){
            $user->setConfirmationToken(null);
            $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
            $pass_plain = uniqid();
            $password = $encoder->encodePassword($pass_plain, $user->getSalt());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();
            $this->sendNewPasswordEmail($user, $pass_plain);
            return $this->render('ZizooUserBundle:User:reset_password_confirm.html.twig');
        } else {
            return $this->redirect($this->generateUrl('login'));
        }
    }
    
    /**
     * Reset password confirmed.
     * 
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function resetPasswordConfirmAction(){
        return $this->render('ZizooUserBundle:User:reset_password_email.html.twig');
    }
    
    
    /**
     * Send email to user with link for generating new password.
     * 
     * @param Zizoo\UserBundle\Entity\User $user
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    private function sendForgotPasswordEmail($user){
        $passwordLink = $this->generateUrl('reset_password', array('token' => $user->getConfirmationToken(), 'email' => $user->getEmail()), true);
        $twig = $this->container->get('twig');
        $template = $twig->loadTemplate('ZizooUserBundle:User:email_password_confirm.html.twig');
        $context = array('link' => $passwordLink);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->container->getParameter('email_password'))
            ->setTo($user->getEmail());

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $this->get('mailer')->send($message);
    }
    
    /**
     * Send email to user with new password.
     * 
     * @param Zizoo\UserBundle\Entity\User $user
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    private function sendNewPasswordEmail($user, $pass){
        $twig = $this->container->get('twig');
        $template = $twig->loadTemplate('ZizooUserBundle:User:email_password_new.html.twig');
        $context = array('pass' => $pass);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->container->getParameter('email_password'))
            ->setTo($user->getEmail());

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $this->get('mailer')->send($message);
    }
    
    /**
     * Change password. Must only be allowed when user is logged in!
     * 
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function changePasswordAction(){
        $user = $this->getUser();
        $form = $this->createForm(new UserNewPasswordType());
        $request = $this->getRequest();
        // If submit
        if ($request->isMethod('POST')) {
            $form->bindRequest($request);
            
            $newUser = $form->getData();
            if ($form->isValid()) {
                $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
                $allegedCurrentPassword = $encoder->encodePassword($newUser->getPassword(), $user->getSalt());
                if ($allegedCurrentPassword==$user->getPassword()){
                    $newPassword = $newUser->getNewPassword();
                    $newPassword = $encoder->encodePassword($newPassword, $user->getSalt());
                    $user->setPassword($newPassword);
                    $em = $this->getDoctrine()
                               ->getEntityManager();
                    $em->persist($user);
                    $em->flush();

                    $trans = $this->get('translator');
                    $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_user.message.password_changed'));
                    return $this->redirect($this->generateUrl('change_password'));

                } else {
                    $trans = $this->get('translator');
                    $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_user.message.password_not_changed'));
                    return $this->redirect($this->generateUrl('change_password'));
                }
            }
   
        }
        return $this->render('ZizooUserBundle:User:change_password.html.twig', array('form' => $form->createView()));
    }
   
}

?>
