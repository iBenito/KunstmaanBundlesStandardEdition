<?php
namespace Zizoo\BoatBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

class MessageOwner
{
    
    protected $boatId;
    
    protected $guestId;
     
    protected $reservationRange;
    
    protected $num_guests;
    
    protected $message;

    public function __construct($boat_id, $guestId) {
        $this->boatId   = $boat_id;
        $this->guestId  = $guestId;
    }
    
    public function getBoatId(){
        return $this->boatId;
    }
    
    public function getGuestId()
    {
        return $this->guestId;
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
    
    public function getMessage()
    {
        return $this->message;
    }
    
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
    
}


?>
