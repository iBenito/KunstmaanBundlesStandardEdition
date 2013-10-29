<?php
namespace Zizoo\SmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ProfileSmsVerify extends SmsVerifyBase {
    
    /**
     * @ORM\OneToOne(targetEntity="Zizoo\ProfileBundle\Entity\Profile", inversedBy="verification")
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