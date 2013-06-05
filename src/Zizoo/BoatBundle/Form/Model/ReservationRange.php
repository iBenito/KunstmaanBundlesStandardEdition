<?php
namespace Zizoo\BoatBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

class ReservationRange
{
    
    protected $reservation_from;
    protected $reservation_to;
    
    public function getReservationFrom()
    {
        return $this->reservation_from;
    }
    
    public function setReservationFrom($from)
    {
        $this->reservation_from = $from;
        return $this;
    }
    
    public function getReservationTo()
    {
        return $this->reservation_to;
    }
    
    public function setReservationTo($to)
    {
        $this->reservation_to = $to;
        return $this;
    }
    
}


?>
