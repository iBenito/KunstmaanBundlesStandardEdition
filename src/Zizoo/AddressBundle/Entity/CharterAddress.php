<?php
namespace Zizoo\AddressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CharterAddress extends AddressBase {
    
    /**
     * @ORM\OneToOne(targetEntity="Zizoo\CharterBundle\Entity\Charter", inversedBy="address")
     */
    protected $charter;
    

    /**
     * Set charter
     *
     * @param \Zizoo\CharterBundle\Entity\Charter $charter
     * @return CharterAddress
     */
    public function setCharter(\Zizoo\CharterBundle\Entity\Charter $charter = null)
    {
        $this->charter = $charter;
    
        return $this;
    }

    /**
     * Get charter
     *
     * @return \Zizoo\CharterBundle\Entity\Charter
     */
    public function getCharter()
    {
        return $this->charter;
    }
 
}