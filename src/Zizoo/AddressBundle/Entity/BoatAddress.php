<?php
namespace Zizoo\AddressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Zizoo\AddressBundle\Entity\BoatAddressRepository")
 */
class BoatAddress extends AddressBase {
    
    /**
     * @ORM\OneToOne(targetEntity="Zizoo\BoatBundle\Entity\Boat", inversedBy="address")
     */
    protected $boat;
    
    /**
     * Set boat
     *
     * @param \Zizoo\BoatBundle\Entity\Boat $boat
     * @return BoatAddress
     */
    public function setBoat(\Zizoo\BoatBundle\Entity\Boat $boat = null)
    {
        $this->boat = $boat;
    
        return $this;
    }

    /**
     * Get boat
     *
     * @return \Zizoo\BoatBundle\Entity\Boat 
     */
    public function getBoat()
    {
        return $this->boat;
    }

}