<?php

namespace Zizoo\MessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EmailController extends Controller
{
    
    
    public function emailTestAction()
    {
        return $this->render('ZizooMessageBundle:Email:email_base.html.twig', array(
            
        ));
    }
    
    public function emailTest2Action()
    {
        $twig = $this->get('twig');
        $templateHtml   = $twig->loadTemplate('ZizooMessageBundle:Email:test.html.twig');
        $templateTxt    = $twig->loadTemplate('ZizooMessageBundle:Email:test.txt.twig');
        
        $context = array(   
                            'sender'    => 'me',
                            'recipient' => 'you',
                            'test'      => 'test');
        
        $htmlBody = $templateHtml->render($context);
        $textBody = $templateTxt->render($context);
        
        $subject = $templateHtml->renderBlock('subject', $context);
        
        
        $email = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->container->getParameter('email_info'))
            ->setTo('alexf83@gmail.com');

        if (!empty($htmlBody)) {
            $email->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $email->setBody($textBody);
        }

        $this->container->get('mailer')->send($email);
        
    }
    

}
?>
