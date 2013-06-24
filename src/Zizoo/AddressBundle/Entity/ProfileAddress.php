<?php
namespace Zizoo\AddressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ProfileAddress extends AddressBase {
    
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\ProfileBundle\Entity\Profile", inversedBy="address")
     */
    protected $profile;
    

    /**
     * Set profile
     *
     * @param \Zizoo\ProfileBundle\Entity\Profile $profile
     * @return ProfileAddress
     */
    public function setProfile(\Zizoo\ProfileBundle\Entity\Profile $profile = null)
    {
        $this->profile = $profile;
    
        return $this;
    }

    /**
     * Get profile
     *
     * @return \Zizoo\ProfileBundle\Entity\Profile 
     */
    public function getProfile()
    {
        return $this->profile;
    }
 
}