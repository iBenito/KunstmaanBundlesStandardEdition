<?php
namespace Zizoo\MessageBundle\Service;

use Zizoo\MessageBundle\Entity\Message;
use Zizoo\MessageBundle\Entity\MessageRecipient;
use Zizoo\ProfileBundle\Entity\Profile;

use Doctrine\Common\Collections\ArrayCollection;

class Messenger {
    
    private $em;
    
    public function __construct($em) {
        $this->em = $em;
    }
    
    private function initMessage(Profile $sender, $body, $subject=null, Message $previous=null){
        
        if ($previous){
            $message = $previous->getReplyMessage();
            $message->setReplyToMessage($previous);
            if ($subject){
                $message->setSubject($subject);
            }
        } else {
            $message = new Message();
            if ($subject){
                $message->setSubject($subject);
            }
        }
        
        $message->setBody($body);
        $message->setSenderProfile($sender);
        
        return $message;
    }
    
    
    public function sendMessage(Profile $sender, ArrayCollection $recipients, $body, $subject=null, Message $previous=null){
        $message = $this->initMessage($sender, $body, $subject, $previous);
        
        foreach ($recipients as $recipient){
            $messageRecipient = new MessageRecipient();
            $messageRecipient->setRecipientProfile($recipient);
            $messageRecipient->setMessage($message);
            $message->addRecipient($messageRecipient);
        }
        
        $messageRecipients = $message->getRecipients();
        foreach ($messageRecipients as $messageRecipient){
            $this->em->persist($messageRecipient);
        }
        
        $this->em->persist($message);
        $this->em->flush();
        
        return $message;
    }
    
    public function sendMessageTo(Profile $sender, Profile $recipient, $body, $subject=null, $previous=null){
        $message = $this->initMessage($sender, $body, $subject, $previous);
        
        $messageRecipient = new MessageRecipient();
        $messageRecipient->setRecipientProfile($recipient);
        $messageRecipient->setMessage($message);
        $message->addRecipient($messageRecipient);
        
        $this->em->persist($messageRecipient);
        $this->em->persist($message);
        $this->em->flush();
        
        return $message;
    }
    
}
?>
