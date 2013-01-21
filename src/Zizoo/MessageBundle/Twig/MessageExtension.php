<?php

namespace Zizoo\MessageBundle\Twig;

use Zizoo\MessageBundle\Entity\Message;
use Zizoo\MessageBundle\Entity\MessageRecipient;
use Zizoo\ProfileBundle\Entity\Profile;

class MessageExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'senderKeep' => new \Twig_Filter_Method($this, 'senderKeep'),
        );
    }
    
    

    public function senderKeep($message, $profile){
        if ($message->getSenderProfile()->getId()==$profile->getId()){
            return $message->getSenderKeep();
        } else {
            $messageRecipients = $message->getRecipients();
            foreach ($messageRecipients as $messageRecipient){
                if ($messageRecipient->getRecipientProfile()->getId()==$profile->getId()){
                    return $messageRecipient->getRecipientKeep();
                }
            }
        }
    }
    
    public function getName()
    {
        return 'message_extension';
    }
}
?>
