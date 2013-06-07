<?php
namespace Zizoo\BoatBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

class BookBoat
{
    
    protected $boat_id;
    
    protected $guestId;
    
    protected $crew;
    
    protected $reservationRange;
    
    protected $num_guests;
    
    protected $subtotal;
    protected $crew_price;
    protected $total;
 
    public function __construct($boat_id) {
        $this->boat_id = $boat_id;
    }
    
    public function getBoatId(){
        return $this->boat_id;
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
    
    public function setCrew($crew)
    {
        $this->crew = $crew;
        return $this;
    }
    
    public function getCrew()
    {
        return $this->crew;
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
    
    public function getNumGuests()
    {
        return $this->num_guests;
    }
    
    public function setNumGuests($num)
    {
        $this->num_guests = $num;
        return $this;
    }
    
//    public function getPrice($availability){
//        $from   = $this->getReservationFrom();
//        $to     = $this->getReservationTo();
//        if (!$from || !$to) return null;
//        $interval = $from->diff($to);
//        return $availability->getPrice() * $interval->days;
//    }
    
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
        return $this;
    }
    
    public function getSubtotal()
    {
        return $this->subtotal;
    }
    
    public function setCrewPrice($crewPrice)
    {
        $this->crew_price = $crewPrice;
        return $this;
    }
    
    public function getCrewPrice()
    {
        return $this->crew_price;
    }
    
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }
    
    public function getTotal()
    {
        return $this->total;
    }
}


?>
