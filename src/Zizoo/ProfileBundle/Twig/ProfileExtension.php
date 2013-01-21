<?php

namespace Zizoo\ProfileBundle\Twig;

use Zizoo\MessageBundle\Entity\Message;
use Zizoo\MessageBundle\Entity\MessageRecipient;
use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\UserBundle\Entity\User;

class ProfileExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'threadDisplay' => new \Twig_Filter_Method($this, 'threadDisplay'),
        );
    }
    
    

    public function threadDisplay($message, $profile){
        if ($message->getSenderProfile()->getId()==$profile->getId()){
            return $profile->getUser()->getUsername();
        } else {
            $messageRecipients = $message->getRecipients();
            foreach ($messageRecipients as $messageRecipient){
                $recipientProfile = $messageRecipient->getRecipientProfile();
                if ($recipientProfile->getId()==$profile->getId()){
                    return $message->getSenderProfile()->getUser()->getUsername();
                }
            }
        }
    }
    
    public function getName()
    {
        return 'profile_extension';
    }
}
?>
