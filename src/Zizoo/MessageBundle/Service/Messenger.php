<?php
namespace Zizoo\MessageBundle\Service;

use Zizoo\MessageBundle\Entity\Message;
use Zizoo\MessageBundle\Entity\Thread;
use Zizoo\MessageBundle\Entity\MessageRecipient;
use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\ProfileBundle\Entity\Profile\NotificationSettings;
use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\UserBundle\Entity\User;
use Zizoo\UserBundle\Form\Model\Invitation;

use FOS\MessageBundle\FormModel\AbstractMessage;
use FOS\MessageBundle\Model\ParticipantInterface;

use Doctrine\Common\Collections\ArrayCollection;

class Messenger {
    
    private $em;
    private $container;
    
    public function __construct($em, $container) {
        $this->em           = $em;
        $this->container    = $container;
    }
        
    public function sendNotificationMessageEmail(Profile $from, Profile $to, Message $message){
        if ($to->getUser()->getCharter()){
            $messageLink = $this->container->get('router')->generate('ZizooBaseBundle_Dashboard_CharterViewThread', array('threadId' => $message->getThread()->getId()), true);
        } else {
            $messageLink = $this->container->get('router')->generate('ZizooBaseBundle_Dashboard_ViewThread', array('threadId' => $message->getThread()->getId()), true);
        }
        $twig = $this->container->get('twig');
        $templateHtml   = $twig->loadTemplate('ZizooMessageBundle:Email:new_message.html.twig');
        $templateTxt    = $twig->loadTemplate('ZizooMessageBundle:Email:new_message.txt.twig');
        
        $context = array(   'link'      => $messageLink,
                            'sender'    => $from,
                            'recipient' => $to,
                            'message'   => $message);
        
        
        $subject = $templateHtml->renderBlock('subject', $context);
        
        $textBody = $templateTxt->render($context);
        $htmlBody = $templateHtml->render($context);

        $from = $this->container->getParameter('email_info');
        $name = $this->container->hasParameter('email_info_name') ? $this->container->getParameter('email_info_name') : null;
        $email = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from, $name)
            ->setTo($to->getUser()->getEmail());

        if (!empty($htmlBody)) {
            $email->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $email->setBody($textBody);
        }

        $this->container->get('mailer')->send($email);
    }
    
    public function sendNotificationBookingEmail(User $to, Reservation $reservation){
        $bookingLink = $this->container->get('router')->generate('ZizooBookingBundle_view_booking', array('id' => $reservation->getId()), true);
        $twig = $this->container->get('twig');
        $templateHtml   = $twig->loadTemplate('ZizooMessageBundle:Email:new_booking.html.twig');
        $templateTxt    = $twig->loadTemplate('ZizooMessageBundle:Email:new_booking.txt.twig');
        
        $context = array(   'bookingLink'      => $bookingLink );
        $subject = $templateHtml->renderBlock('subject', $context);
        $textBody = $templateHtml->render($context);
        $htmlBody = $templateTxt->render($context);

        $from = $this->container->getParameter('email_info');
        $name = $this->container->hasParameter('email_info_name') ? $this->container->getParameter('email_info_name') : null;
        $email = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from, $name)
            ->setTo($to->getEmail());

        if (!empty($htmlBody)) {
            $email->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $email->setBody($textBody);
        }

        $this->container->get('mailer')->send($email);
    }
    
    
    public function sendInvitationEmail($to, User $from){
        $inviteLink = $this->container->get('router')->generate('ZizooUserBundle_register', array('email' => $to), true);
        $twig = $this->container->get('twig');

        $templateHtml   = $twig->loadTemplate('ZizooUserBundle:Email:invite.html.twig');
        $templateTxt    = $twig->loadTemplate('ZizooUserBundle:Email:invite.txt.twig');
        
        $context = array(   'link'      => $inviteLink,
                            'sender'    => $from);
        
        $subject = $templateHtml->renderBlock('subject', $context);
        
        $textBody = $templateTxt->render($context);
        $htmlBody = $templateHtml->render($context);

        $from = $this->container->getParameter('email_info');
        $name = $this->container->hasParameter('email_info_name') ? $this->container->getParameter('email_info_name') : null;
        $email = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from, $name)
            ->setTo($to);

        if (!empty($htmlBody)) {
            $email->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $email->setBody($textBody);
        }

        $this->container->get('mailer')->send($email);
    }
    
    public function sendRegistrationEmail(User $to){
        $link = $this->container->get('router')->generate('ZizooBaseBundle_homepage', array(), true);
        $twig = $this->container->get('twig');

        $templateHtml   = $twig->loadTemplate('ZizooUserBundle:Email:welcome.html.twig');
        $templateTxt    = $twig->loadTemplate('ZizooUserBundle:Email:welcome.txt.twig');
        
        $context = array(   'link'      => $link,
                            'recipient' => $to);
        
        $subject = $templateHtml->renderBlock('subject', $context);
        $textBody = $templateTxt->render($context);
        $htmlBody = $templateHtml->render($context);

        $from = $this->container->getParameter('email_register');
        $name = $this->container->hasParameter('email_register_name') ? $this->container->getParameter('email_register_name') : null;
        $email = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from, $name)
            ->setTo($to->getEmail());

        if (!empty($htmlBody)) {
            $email->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $email->setBody($textBody);
        }

        $this->container->get('mailer')->send($email);
    }
        
    
    /**
     * Send email to registration user, with activation link
     * 
     * @param Zizoo\UserBundle\Entity\User $user
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function sendConfirmationEmail(User $user){
        $isCharter = $user->getCharter()!=null;
        $confirmRoute = $isCharter?'ZizooCharterBundle_Registration_confirm':'ZizooUserBundle_confirm';
        $activationLink = $this->container->get('router')->generate($confirmRoute, array('token' => $user->getConfirmationToken(), 'email' => $user->getEmail()), true);
        $twig = $this->container->get('twig');
        
        $templateHtmlLocation = $isCharter?'ZizooUserBundle:Email:confirm_charter.html.twig':'ZizooUserBundle:Email:confirm.html.twig';
        $templateTxtLocation = $isCharter?'ZizooUserBundle:Email:confirm_charter.txt.twig':'ZizooUserBundle:Email:confirm.txt.twig';
        
        $templateHtml   = $twig->loadTemplate($templateHtmlLocation);
        $templateTxt    = $twig->loadTemplate($templateTxtLocation);
        
     
        $context = array('link' => $activationLink);
        
        $subject = $templateHtml->renderBlock('subject', $context);
        
        $textBody = $templateTxt->render($context);
        $htmlBody = $templateHtml->render($context);

        $from = $this->container->getParameter('email_info');
        $name = $this->container->hasParameter('email_register_name') ? $this->container->getParameter('email_register_name') : null;
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from, $name)
            ->setTo($user->getEmail());

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $this->container->get('mailer')->send($message);
    }
    
    
    /**
     * Send email to user wanting to change email address, with confirmation link
     * 
     * @param Zizoo\UserBundle\Entity\User $user
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function sendChangeEmailConfirmationEmail(User $user){
        $activationLink = $this->container->get('router')->generate('ZizooUserBundle_change_email_confirm', array('token' => $user->getChangeEmailToken(), 'email' => $user->getEmail()), true);
        $twig = $this->container->get('twig');
        
        $templateHtml   = $twig->loadTemplate('ZizooUserBundle:Email:change_email_confirm.html.twig');
        $templateTxt    = $twig->loadTemplate('ZizooUserBundle:Email:change_email_confirm.txt.twig');
        
        $context = array('link' => $activationLink);
        $subject = $templateHtml->renderBlock('subject', $context);
        
        $textBody = $templateTxt->render($context);
        $htmlBody = $templateHtml->render($context);

        $from = $this->container->getParameter('email_info');
        $name = $this->container->hasParameter('email_register_name') ? $this->container->getParameter('email_register_name') : null;
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from, $name)
            ->setTo($user->getEmail());

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $this->container->get('mailer')->send($message);
    }
    
    /**
     * Send email to user with link for generating new password.
     * 
     * @param Zizoo\UserBundle\Entity\User $user
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function sendForgotPasswordEmail(User $user){
        $passwordLink = $this->container->get('router')->generate('ZizooUserBundle_reset_password', array('token' => $user->getConfirmationToken(), 'email' => $user->getEmail()), true);
        $twig = $this->container->get('twig');

        $templateHtml   = $twig->loadTemplate('ZizooUserBundle:Email:password_confirm.html.twig');
        $templateTxt    = $twig->loadTemplate('ZizooUserBundle:Email:password_confirm.txt.twig');
        
        $context = array('link' => $passwordLink);
        
        $subject = $templateHtml->renderBlock('subject', $context);
        
        $textBody = $templateTxt->render($context);
        $htmlBody = $templateHtml->render($context);

        $from = $this->container->getParameter('email_password');
        $name = $this->container->hasParameter('email_password_name') ? $this->container->getParameter('email_password_name') : null;
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from, $name)
            ->setTo($user->getEmail());

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $this->container->get('mailer')->send($message);
    }
    
    /**
     * Send email to user with new password.
     * 
     * @param Zizoo\UserBundle\Entity\User $user
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function sendNewPasswordEmail(User $user, $pass){
        $twig = $this->container->get('twig');

        $templateHtml   = $twig->loadTemplate('ZizooUserBundle:Email:password_new.html.twig');
        $templateTxt    = $twig->loadTemplate('ZizooUserBundle:Email:password_new.txt.twig');
        
        $context = array('pass' => $pass);
        
        $subject = $templateHtml->renderBlock('subject', $context);
        
        $textBody = $templateTxt->render($context);
        $htmlBody = $templateHtml->render($context);

        $from = $this->container->getParameter('email_password');
        $name = $this->container->hasParameter('email_password_name') ? $this->container->getParameter('email_password_name') : null;
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from, $name)
            ->setTo($user->getEmail());

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $this->container->get('mailer')->send($message);
    }
    
    public function threadAllowed(ParticipantInterface $sender, AbstractMessage $message)
    {
        $contactRepo = $this->container->get('doctrine.orm.entity_manager')->getRepository('ZizooMessageBundle:Contact');
        
        $recipients = $message->getRecipients();
        foreach ($recipients as $recipient){
            $contact = $contactRepo->findOneBy( array(    'sender'    => $sender,
                                                            'recipient' => $recipient) );
            if (!$contact) return false;
        }
        return true;
    }
    
}
?>
