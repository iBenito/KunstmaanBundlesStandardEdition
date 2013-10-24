<?php

namespace Zizoo\CharterBundle\Form\Model;

class DenyBooking
{
    protected $denyMessage;
    
    public function setDenyMessage($message)
    {
        $this->denyMessage = $message;
        return $this;
    }
    
    public function getDenyMessage()
    {
        return $this->denyMessage;
    }
}
