<?php
namespace Zizoo\MessageBundle\Service;

use Zizoo\MessageBundle\Entity\Message;
use Zizoo\MessageBundle\Entity\MessageRecipient;
use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\ProfileBundle\Entity\Profile\NotificationSettings;
use Zizoo\BookingBundle\Entity\Reservation;

use Doctrine\Common\Collections\ArrayCollection;

class Messenger {
    
    private $em;
    private $container;
    
    public function __construct($em, $container) {
        $this->em           = $em;
        $this->container    = $container;
    }
        
    private function sendNotificationMessageEmail(Profile $from, Profile $to, Message $message){
        $messageLink = $this->container->get('router')->generate('inbox', array('messageId' => $message->getId()), true);
        $twig = $this->container->get('twig');
        $template = $twig->loadTemplate('ZizooMessageBundle:Email:new_message.html.twig');
        $context = array(   'link'      => $messageLink,
                            'sender'    => $from,
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
    
    private function sendNotificationBookingEmail(Reservation $reservation, Profile $from, Profile $to){
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
        
    private function initMessage(Profile $sender, $body, $subject=null, $type, Message $previous=null, $setRecipient=true){
        
        if ($previous){
            $message = $previous->getReplyMessage($setRecipient);
            $message->setReplyToMessage($previous);
            if ($subject){
                $message->setSubject($subject);
            }
            if ($previous->getThreadRootMessage()){
                $message->setThreadRootMessage($previous->getThreadRootMessage());
            } else {
                $message->setThreadRootMessage($previous->getId());
            }
        } else {
            $message = new Message();
            $message->setType($type);
            if ($subject){
                $message->setSubject($subject);
            }
        }
        
        $message->setBody($body);
        $message->setSenderProfile($sender);
        
        return $message;
    }
    
    
    private function processMultipleRecipientsMessage($message, $recipients){
        foreach ($recipients as $recipient){
            $alreadyRecipient = false;
            $messageRecipients = $message->getRecipients();
            foreach ($messageRecipients as $messageRecipient){
                if ($messageRecipient->getRecipientProfile()->getId()==$recipient->getId()){
                    $alreadyRecipient = true;
                    break;
                }
            }
            if (!$alreadyRecipient){
                $messageRecipient = new MessageRecipient();
                $messageRecipient->setRecipientProfile($recipient);
                $messageRecipient->setMessage($message);
                $message->addRecipient($messageRecipient);
            }
        }
        
        $messageRecipients = $message->getRecipients();
        foreach ($messageRecipients as $messageRecipient){
            $this->em->persist($messageRecipient);
        }
        
        $this->em->persist($message);
        $this->em->flush();
        
        return $message;
    }
    
    public function sendMessage(Profile $sender, ArrayCollection $recipients, $body, $subject=null, Message $previous=null, $setRecipient=true){
        $message = $this->initMessage($sender, $body, $subject, Message::MESSAGE, $previous, $setRecipient);
        $message = $this->processMultipleRecipientsMessage($message, $recipients);
        
        $messageRecipients = $message->getRecipients();
        foreach ($messageRecipients as $messageRecipient){
            $notificationSettings = $messageRecipient->getRecipientProfile()->getNotificationSettings();
            if ($notificationSettings->getMessage()){
                $this->sendNotificationMessageEmail($sender, $messageRecipient->getRecipientProfile(), $message);
            }
        }
        
        return $message;
    }
    
    public function sendMessageTo(Profile $sender, Profile $recipient, $body, $subject=null, Message $previous=null, $setRecipient=true){
        return $this->sendMessage($sender, new ArrayCollection(array($recipient)), $body, $subject, $previous, $setRecipient);
    }
    
    public function sendReservationMessage(Reservation $reservation, Profile $sender, ArrayCollection $recipients, $body, $subject=null, Message $previous=null, $setRecipient=true){
        $message = $this->initMessage($sender, $body, $subject, Message::BOOKING, $previous, $setRecipient);
        $message = $this->processMultipleRecipientsMessage($message, $recipients);
        
        $messageRecipients = $message->getRecipients();
        foreach ($messageRecipients as $messageRecipient){
            $notificationSettings = $messageRecipient->getRecipientProfile()->getNotificationSettings();
            if ($notificationSettings->getMessage()){
                $this->sendNotificationBookingEmail($reservation, $sender, $messageRecipient->getRecipientProfile());
            }
        }
        
        return $message;
    }
    
    public function sendReservationMessageTo(Reservation $reservation, Profile $sender, Profile $recipient, $body, $subject=null, Message $previous=null, $setRecipient=true){
        return $this->sendReservationMessage($reservation, $sender, new ArrayCollection(array($recipient)), $body, $subject, $previous, $setRecipient);
    }
    
    
    
    
    public function deleteReceivedThread(Profile $recipient, $thread, $delete=true){
        foreach ($thread as $threadMessage){
            $messageRecipients = $threadMessage->getRecipients();
            foreach ($messageRecipients as $messageRecipient){
                if ($messageRecipient->getRecipientProfile()->getId()==$recipient->getId()){
                    $messageRecipient->setRecipientKeep(!$delete);
                    $this->em->persist($messageRecipient);
                }
            }
        }
        $this->em->flush();
    }
    
    public function deleteSentThread(Profile $sender, $thread, $delete=true){
        foreach ($thread as $threadMessage){
            if ($threadMessage->getSenderProfile()->getId()==$sender->getId()){
                $threadMessage->setSenderKeep(!$delete);
                $this->em->persist($threadMessage);
            }
        }
        $this->em->flush();
    }
    
    public function deleteReceivedMessage(Profile $recipient, Message $message, $delete=true){
        $deleteThread = true;
        if ($deleteThread){
            $thread = $this->em->getRepository('ZizooMessageBundle:Message')->getMessageThread($message);
            $this->deleteReceivedThread($recipient, $thread, $delete);
        } else {
            $this->deleteReceivedThread($recipient, array($message), $delete);
        }
    }
    
    public function deleteSentMessage(Profile $sender, Message $message, $delete=true){
        $deleteThread=true;
        if ($deleteThread){
            $thread = $this->em->getRepository('ZizooMessageBundle:Message')->getMessageThread($message);
            $this->deleteReceivedThread($sender, $thread, $delete);
        } else {
            $this->deleteReceivedThread($sender, array($message), $delete);
        }
    }
    

    public function markReceivedMessage(Profile $recipient, Message $message, $read){
        $recipients = $message->getRecipients();
        foreach ($recipients as $recipient){
            if ($recipient->getId()==$recipient->getId()){
                if ($read){
                    $recipient->setRecipientReadDate(new \DateTime());
                } else {
                    $recipient->setRecipientReadDate(null);
                }
                $this->em->persist($recipient);
            }
        }
        
        $this->em->flush();
        
        return $message;
    }
    
}
?>
