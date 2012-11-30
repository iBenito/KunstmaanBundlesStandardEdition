<?php

namespace Zizoo\UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

use Zizoo\UserBundle\Form\Type\RegistrationType;
use Zizoo\UserBundle\Form\Model\Registration;
use Zizoo\UserBundle\Entity\Group;

class RegistrationController extends Controller
{
   
    public function registerAction()
    {
        
        $form = $this->createForm(new RegistrationType(), new Registration());
        $request = $this->getRequest();
        
        if ($request->isMethod('POST')) {
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                
                $data = $form->getData();
                $user = $data->getUser();
                $user->setSalt(md5(time()));
                $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
                $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($password);
                $user->setUsername($user->getEmail());
                //$confirmationToken = $encoder->encodePassword($user->getUserName(), $user->getSalt());
                $user->setConfirmationToken(uniqid());
                $em = $this->getDoctrine()
                            ->getEntityManager();
                
                $zizooUserGroup = $em->getRepository('ZizooUserBundle:Group')->findOneByRole('ROLE_ZIZOO_USER');
                
                $user->addGroup($zizooUserGroup);
                                
                $em->persist($zizooUserGroup);
                $em->persist($user);
                $em->flush();

                $activationLink = $this->generateUrl('confirm', array('token' => $user->getConfirmationToken(), 'email' => $user->getEmail()), true);
                $twig = $this->container->get('twig');
                $template = $twig->loadTemplate('ZizooUserBundle:Registration:email_confirm.html.twig');
                $context = array('link' => $activationLink);
                $subject = $template->renderBlock('subject', $context);
                $textBody = $template->renderBlock('body_text', $context);
                $htmlBody = $template->renderBlock('body_html', $context);
                
                $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom('info@alex-fuckert.net')
                    ->setTo($user->getEmail());
                
                if (!empty($htmlBody)) {
                    $message->setBody($htmlBody, 'text/html')
                        ->addPart($textBody, 'text/plain');
                } else {
                    $message->setBody($textBody);
                }
                
                $this->get('mailer')->send($message);
                
                return $this->redirect($this->generateUrl('submitted'));
            }
        }
        
        return $this->render('ZizooUserBundle:User:register.html.twig', array('form' => $form->createView()));
    }
    
    public function submittedAction(){
        
        return $this->render('ZizooUserBundle:User:submitted.html.twig');
    }
    
    public function confirmAction($token, $email){
        $em = $this->getDoctrine()
                   ->getEntityManager();
        
        
        
        $user = $em->getRepository('ZizooUserBundle:User')->findOneByEmail($email);
        
        if ($user && $user->getConfirmationToken()===$token){
            $user->setConfirmationToken(null);
            $user->setIsActive(1);
            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('confirmed'));
        } else {
            return $this->redirect($this->generateUrl('register'));
        }
        
    }
    
    public function confirmedAction(){

        $user = $this->getUser();
        return $this->render('ZizooUserBundle:User:confirmed.html.twig', array('user' => $user));
    }
    
}
