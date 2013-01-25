<?php
namespace Zizoo\MessageBundle\Service;

use Zizoo\MessageBundle\Entity\Message;
use Zizoo\MessageBundle\Entity\MessageRecipient;
use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\ProfileBundle\Entity\Profile\NotificationSettings;
use Zizoo\BookingBundle\Entity\Reservation;
use Zizoo\UserBundle\Entity\User;
use Zizoo\UserBundle\Form\Model\Invitation;

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
    
    public function sendNotificationBookingEmail(Reservation $reservation, Profile $from, Profile $to){
        //$messageLink = $this->container->get('router')->generate('view_thread', array('messageId' => $message->getId()));
        $twig = $this->container->get('twig');
        $template = $twig->loadTemplate('ZizooBookingBundle:Email:new_booking.html.twig');
        $context = array(   'booking'      => $reservation->getId(),);
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
    
    
    public function sendInvitationEmail(Invitation $invitation, User $from){
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
            ->setTo($invitation->getEmail1());

        if ($invitation->getEmail2()!='') $email->addTo($invitation->getEmail2());
        if ($invitation->getEmail3()!='') $email->addTo($invitation->getEmail3());
        if ($invitation->getEmail4()!='') $email->addTo($invitation->getEmail4());
        if ($invitation->getEmail5()!='') $email->addTo($invitation->getEmail5());
        
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
