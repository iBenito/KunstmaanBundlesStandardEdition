<?php
namespace Zizoo\AddressBundle\Form\Model;

class FilterBoat
{
    protected $boat_type;
  
    protected $length_from;
    protected $length_to;
    
    protected $num_cabins_from;
    protected $num_cabins_to;
  
    
    public function getBoatType()
    {
        return $this->boat_type['boat_type'];
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
