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
    
    protected $confirmed;
 
    public function __construct(Boat $boat=null) {
        $this->boat  = $boat;
    }
    
    public function getBoat(){
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
    
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;
        return $this;
    }
    
    public function getConfirmed()
    {
        return $this->confirmed;
    }
}


?>
