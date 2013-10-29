<?php

namespace Zizoo\CharterBundle\Form\Model;

use Zizoo\CharterBundle\Form\Model\DenyBooking;

class AcceptBooking
{
    protected $acceptMessage;
    protected $denyBooking;
    protected $overlappingBookingRequests;
    
    public function __construct($overlappingBookingRequests) 
    {
        $this->overlappingBookingRequests = $overlappingBookingRequests;
    }
    
    public function getOverlappingBookingRequests()
    {
        return $this->overlappingBookingRequests;
    }
    
    public function setDenyBooking(DenyBooking $denyBooking)
    {
        $this->denyBooking = $denyBooking;
        return $this;
    }
    
    public function getDenyBooking()
    {
        return $this->denyBooking;
    }
        
    public function setAcceptMessage($message)
    {
        $this->acceptMessage = $message;
    }
    
    public function getAcceptMessage()
    {
        return $this->acceptMessage;
    }
    
}
