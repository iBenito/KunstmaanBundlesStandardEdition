<?php

namespace Zizoo\ReservationBundle\Form\Model;

class DenyReservation
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
