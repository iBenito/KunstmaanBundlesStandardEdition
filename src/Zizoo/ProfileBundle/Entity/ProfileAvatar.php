<?php
namespace Zizoo\ProfileBundle\Entity;

use Zizoo\ProfileBundle\Entity\Profile;

use Zizoo\MediaBundle\Entity\Media;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ProfileAvatar extends Media {
    
    /**
     * @ORM\ManyToOne(targetEntity="\Zizoo\ProfileBundle\Entity\Profile", inversedBy="avatar")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     */
    protected $profile;
    

    /**
     * Set profile
     *
     * @param \Zizoo\ProfileBundle\Entity\Profile $profile
     * @return ProfileAddress
     */
    public function setProfile(Profile $profile = null)
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

    public function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'images/profile/'.$this->profile->getId();
    }

}