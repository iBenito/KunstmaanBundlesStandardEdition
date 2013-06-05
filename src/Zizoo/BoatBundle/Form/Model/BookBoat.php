<?php
namespace Zizoo\BoatBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

class BookBoat
{
    
    protected $boatId;
    
    protected $guestId;
    
//    protected $reservation_from;
//    protected $reservation_to;
    protected $reservationRange;
    
    protected $num_guests;
 
    public function __construct($boatId) {
        $this->boatId = $boatId;
    }
    
    public function getBoatId(){
        return $this->boatId;
    }
    
    public function getGuestId()
    {
        return $this->guestId;
    }
    
    public function setGuestId($guestId)
    {
        $this->guestId = $guestId;
        return $this;
    }
    
//    public function getReservationFrom()
//    {
//        return $this->reservation_from;
//    }
//    
//    public function setReservationFrom($from)
//    {
//        $this->reservation_from = $from;
//        return $this;
//    }
//    
//    public function getReservationTo()
//    {
//        return $this->reservation_to;
//    }
//    
//    public function setReservationTo($to)
//    {
//        $this->reservation_to = $to;
//        return $this;
//    }
    
    public function setReservationRange($reservationRange)
    {
        $this->reservationRange = $reservationRange;
        return $this;
    }
    
    public function getReservationRange()
    {
        return $this->reservationRange;
    }
    
    public function getNumGuests()
    {
        return $this->num_guests;
    }
    
    public function setNumGuests($num)
    {
        $this->num_guests = $num;
        return $this;
    }
    
    public function getPrice($availability){
        $from   = $this->getReservationFrom();
        $to     = $this->getReservationTo();
        if (!$from || !$to) return null;
        $interval = $from->diff($to);
        return $availability->getPrice() * $interval->days;
    }
}


?>
