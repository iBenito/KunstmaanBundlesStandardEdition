<?php
namespace Zizoo\AddressBundle\Form\Model;

use Doctrine\Common\Collections\ArrayCollection;

class SearchBoat
{
    
    protected $location;
    
    protected $page;
    
    protected $boat_type;
    
    protected $reservation_from;
    protected $reservation_to;
    
    protected $num_guests;
    
    protected $length_from;
    protected $length_to;
    
    protected $num_cabins_from;
    protected $num_cabins_to;
    
    public function __construct() {
        $this->page = 1;
    }
    
    public function getLocation()
    {
        return $this->location;
    }
    
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }
    
    public function getPage()
    {
        return $this->page;
    }
    
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }
    
    public function getBoatType()
    {
        return $this->boat_type;
    }
    
    public function setBoatType($type)
    {
        $this->boat_type = $type;
        return $this;
    }
    
    public function boatTypeSelected()
    {
        return $this->boat_type['boat_type']->count()>0;
    }
    
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
    
    public function getNumGuests()
    {
        return $this->num_guests;
    }
    
    public function setNumGuests($num)
    {
        $this->num_guests = $num;
        return $this;
    }
    
    public function getLengthFrom()
    {
        return $this->length_from;
    }
    
    public function setLengthFrom($from)
    {
        $this->length_from = $from;
        return $this;
    }
    
    public function getLengthTo()
    {
        return $this->length_to;
    }
    
    public function setLengthTo($to)
    {
        $this->length_to = $to;
        return $this;
    }
    
    public function getNumCabinsFrom()
    {
        return $this->num_cabins_from;
    }
    
    public function setNumCabinsFrom($from)
    {
        $this->num_cabins_from = $from;
        return $this;
    }
    
    public function getNumCabinsTo()
    {
        return $this->num_cabins_to;
    }
    
    public function setNumCabinsTo($to)
    {
        $this->num_cabins_to = $to;
        return $this;
    }
    
    
}


?>
