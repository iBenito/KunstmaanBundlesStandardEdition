<?php
namespace Zizoo\BoatBundle\Form\Model;

use Zizoo\BoatBundle\Entity\Boat;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

class Availability
{
    
    protected $boat;
    
    protected $reservationRange;
    
    protected $type;
    
    protected $price;
    
    protected $denyReservation;
    protected $overlappingReservationRequests;
    protected $overlappingExternalReservations;
    
    protected $overlap;
    protected $confirm;
 
    public function __construct(Boat $boat=null) {
        $this->boat  = $boat;
    }
    
    public function getBoat(){
        return $this->boat;
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
    
    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;
        return $this;
    }
    
    public function getConfirm()
    {
        return $this->confirm;
    }
    
    public function setOverlap($overlap)
    {
        $this->overlap = $overlap;
        return $this;
    }
    
    public function getOverlap()
    {
        return $this->overlap;
    }
}


?>
