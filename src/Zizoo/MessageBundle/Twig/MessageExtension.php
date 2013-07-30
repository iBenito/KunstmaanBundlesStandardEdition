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
            'senderDisplay' => new \Twig_Filter_Method($this, 'senderDisplay'),
        );
    }
    
    

    public function senderDisplay($message, $user)
    {   
        
        $thread         = $message->getThread();
        $userCharter    = $user->getCharter();
        $senderUser     = $message->getSender();
        $senderCharter  = $senderUser->getCharter();
        
        $userIsCharterRep   = false;
        $senderIsCharterRep = false;
        
        if ($thread->getBooking()){
            $charter = $thread->getBooking()->getReservation()->getBoat()->getCharter();
            if ($userCharter && $userCharter->getId() == $charter->getId()){
                $userIsCharterRep = true;
            }
            if ($senderUser && $senderCharter->getId() == $charter->getId()){
                $senderIsCharterRep = true;
            }
        }
        
        
        $userIsSender = $senderUser->getId() == $user->getId();
        if ($userIsSender){
            // User is sender
            if ($userIsCharterRep){
                $ret = array('picture' => $charter->getLogo(), 'name' => $user->getProfile()->getFirstName() . ' @ ' . $userCharter->getCharterName());
            } else {
                $ret = array('picture' => $user->getProfile()->getAvatar()->first(), 'name' => $user->getProfile()->getFirstName());
            }
        } else {
            // User is recipient
            if ($senderIsCharterRep){
                $ret = array('picture' => $senderCharter->getLogo(), 'name' => $senderUser->getProfile()->getFirstName() . ' @ ' . $senderCharter->getCharterName());
            } else {
                $ret = array('picture' => $senderUser->getProfile()->getAvatar()->first(), 'name' => $senderUser->getProfile()->getFirstName());
            }
        }
        return $ret;
        
    }
    
    public function getName()
    {
        return 'message_extension';
    }
}
?>
