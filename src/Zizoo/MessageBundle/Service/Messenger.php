<?php
namespace Zizoo\MessageBundle\Service;

use Zizoo\MessageBundle\Entity\Message;
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
        $messageLink = $this->container->get('router')->generate('fos_message_inbox', array('messageId' => $message->getId()), true);
        $twig = $this->container->get('twig');
        $template = $twig->loadTemplate('ZizooMessageBundle:Email:new_message.html.twig');
        $context = array(   'link'      => $messageLink,
                            'sender'    => $from,
                            'recipient' => $to,
                            'message'   => $message);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $email = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->container->getParameter('email_info'))
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
        $template = $twig->loadTemplate('ZizooBookingBundle:Email:new_booking.html.twig');
        $context = array(   'bookingLink'      => $bookingLink );
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $email = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->container->getParameter('email_info'))
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
        $inviteLink = $this->container->get('router')->generate('ZizooBaseBundle_homepage', array(), true);
        $twig = $this->container->get('twig');
        $template = $twig->loadTemplate('ZizooUserBundle:Email:invite.html.twig');
        
        $context = array(   'link'      => $inviteLink,
                            'sender'    => $from);
        
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $email = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->container->getParameter('email_info'))
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
        $template = $twig->loadTemplate('ZizooUserBundle:Email:welcome.html.twig');
        $context = array(   'link'      => $link,
                            'recipient' => $to);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $email = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->container->getParameter('email_register'))
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
        $activationLink = $this->container->get('router')->generate('ZizooUserBundle_confirm', array('token' => $user->getConfirmationToken(), 'email' => $user->getEmail()), true);
        $twig = $this->container->get('twig');
        $template = $twig->loadTemplate('ZizooUserBundle:Email:confirm.html.twig');
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
        $template = $twig->loadTemplate('ZizooUserBundle:Email:password_confirm.html.twig');
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
        $template = $twig->loadTemplate('ZizooUserBundle:Email:password_new.html.twig');
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
