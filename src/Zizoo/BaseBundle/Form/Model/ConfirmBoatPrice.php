<?php

namespace Zizoo\BaseBundle\Form\Model;

use Zizoo\ReservationBundle\Form\Model\DenyReservation;

class ConfirmBoatPrice
{
    protected $denyReservation;
    protected $overlappingReservationRequests;
    
    public function __construct($overlappingReservationRequests) 
    {
        $this->overlappingReservationRequests = $overlappingReservationRequests;
    }
    
    public function getOverlappingReservationRequests()
    {
        return $this->overlappingReservationRequests;
    }
    
    public function setDenyReservation(DenyReservation $denyReservation)
    {
        $this->denyReservation = $denyReservation;
        return $this;
    }
    
    public function getDenyReservation()
    {
        return $this->denyReservation;
    }
    
}
