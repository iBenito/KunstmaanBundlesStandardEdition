<?php
namespace Zizoo\BoatBundle\Form\Model;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\ReservationBundle\Form\Model\DenyReservation;

class Availability
{
    
    protected $boat;
    
    protected $boat_id;
    
    protected $reservationRange;
    
    protected $type;
    
    protected $price;
    
    protected $denyReservation;
    protected $overlappingReservationRequests;
    protected $overlappingExternalReservations;
    protected $overlappingBookedReservations;
    
    protected $validationGroups;
    
    protected $confirm;
 
    public function __construct(Boat $boat=null) 
    {
        $this->boat  = $boat;
        if ($boat!==null){
            $this->setBoatId($boat->getId());
        }
    }
    
    public function getBoat()
    {
        return $this->boat;
    }
    
    public function setBoatId($boat_id)
    {
        $this->boat_id = $boat_id;
        return $this;
    }
    
    public function getBoatId()
    {
        return $this->boat_id;
    }
    
    
    public function setReservationRange($reservationRange)
    {
        $this->reservationRange = $reservationRange;
        return $this;
    }
    
    public function getReservationRange()
    {
        return $this->reservationRange;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    

    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }
    
    public function getPrice()
    {
        return $this->price;
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
    
    public function setOverlappingExternalReservations($reservations)
    {
        $this->overlappingExternalReservations = $reservations;
        return $this;
    }
    
    public function getOverlappingExternalReservations()
    {
        return $this->overlappingExternalReservations;
    }
    
    public function setOverlappingReservationRequests($reservations)
    {
        $this->overlappingReservationRequests = $reservations;
        return $this;
    }
    
    public function getOverlappingReservationRequests()
    {
        return $this->overlappingReservationRequests;
    }
    
    public function setOverlappingBookedReservations($reservations)
    {
        $this->overlappingBookedReservations = $reservations;
        return $this;
    }
    
    public function getOverlappingBookedReservations()
    {
        return $this->overlappingBookedReservations;
    }
    
    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;
        return $this;
    }
    
    public function getConfirm()
    {
        return $this->confirm;
    }
    
    public function setTest($test)
    {
        $this->test = $test;
        return $this;
    }
    
    public function getTest()
    {
        return $this->test;
    }
}


?>
