<?php
namespace Zizoo\AddressBundle\Form\Model;

use Doctrine\Common\Collections\ArrayCollection;

class SearchBoat
{
    
    protected $location;
    
    protected $page;
    protected $page_size;
   
    protected $reservation_from;
    protected $reservation_to;
    
    protected $num_guests;
    
    protected $filter;


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
    
    public function getPageSize()
    {
        return $this->page_size;
    }
    
    public function setPageSize($size)
    {
        $this->page_size = $size;
        return $this;
    }
    
    public function getReservationFrom()
    {
        return $this->reservation_from;
    }
    
    public function setReservationFrom($from)
    {
        if ($from instanceof \DateTime){
            $from = $from->setTime(12,0,0);
        }
        $this->reservation_from = $from;
        return $this;
    }
    
    public function getReservationTo()
    {
        return $this->reservation_to;
    }
    
    public function setReservationTo($to)
    {
        if ($to instanceof \DateTime){
            $to = $to->setTime(23,59,59);
        }
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
    
}


?>
