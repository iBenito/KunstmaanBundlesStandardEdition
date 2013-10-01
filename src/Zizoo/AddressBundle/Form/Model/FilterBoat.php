<?php
namespace Zizoo\AddressBundle\Form\Model;

class FilterBoat
{
    protected $available_only;
    
    protected $boat_type;
  
    protected $crew;
    
    protected $length_from;
    protected $length_to;
    
    protected $num_cabins_from;
    protected $num_cabins_to;
  
    protected $price_from;
    protected $price_to;
    
    protected $equipment;


    public function getAvailableOnly()
    {
        return count($this->available_only)>0;
    }
    
    public function setAvailableOnly($availableOnly)
    {
        $this->available_only = $availableOnly;
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
    
    public function getCrew()
    {
        return count($this->crew)>0;
    }
    
    public function setCrew($crew)
    {
        $this->crew = $crew;
        return $this;
    }
    
    public function boatTypeSelected()
    {
        return $this->boat_type->count()>0;
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
    
    public function getPriceFrom()
    {
        return $this->price_from;
    }
    
    public function setPriceFrom($from)
    {
        $this->price_from = $from;
        return $this;
    }
    
    public function getPriceTo()
    {
        return $this->price_to;
    }
    
    public function setPriceTo($to)
    {
        $this->price_to = $to;
        return $this;
    }
    
    public function getEquipment()
    {
        return $this->equipment;
    }
    
    public function setEquipment($type)
    {
        $this->equipment = $type;
        return $this;
    }
    
    public function equipmentSelected()
    {
        return $this->equipment->count()>0;
    }
    
    
}


?>
