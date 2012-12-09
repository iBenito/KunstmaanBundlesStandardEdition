<?php

// src/Zizoo/UserBundle/Controller/UserController.php;
namespace Zizoo\UserBundle\Controller;

use Zizoo\UserBundle\Entity\User;
use Zizoo\UserBundle\Form\Type\UserForgotPasswordType;
use Zizoo\UserBundle\Form\Type\UserNewPasswordType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class UserController extends Controller
{
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

        return $this->render('ZizooUserBundle:User:login.html.twig', array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }

    
    public function loginWidgetAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        $isLoggedIn = false;
        $securityContext = $this->container->get('security.context');
        if( $securityContext->isGranted('IS_AUTHENTICATED_FULLY') || $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            $isLoggedIn = true;
        }
        
        $user = $this->getUser();
       
        return $this->render('ZizooUserBundle:User:login_widget.html.twig', array(
            // last username entered by the user
            'user' => $user,
            'logged_in' => $isLoggedIn
        ));
    }
    
    
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
                return $this->render('ZizooUserBundle:User:forgot_password.html.twig', array('form' => $form->createView(), 'error' => 'no_such_user'));
            }
            
            $user->setConfirmationToken(uniqid());
            
            $em->persist($user);
            $em->flush();

            $this->sendForgotPasswordEmail($user);
            
            return $this->redirect($this->generateUrl('reset_password_email'));
        }
        
        return $this->render('ZizooUserBundle:User:forgot_password.html.twig', array('form' => $form->createView()));
    }
    
    public function resetPasswordEmailAction(){
        return $this->render('ZizooUserBundle:User:reset_password_email.html.twig');
    }
    
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
    
    public function resetPasswordConfirmAction(){
        return $this->render('ZizooUserBundle:User:reset_password_email.html.twig');
    }
    
    
    /**
     * Send email to user with link for generating new password.
     * @param type $user
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
     * @param type $user
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
