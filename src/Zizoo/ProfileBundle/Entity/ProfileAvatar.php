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
    
    /**
     * @ORM\Column(name="mime_type", type="text", nullable=false)
     */
    private $mimeType;
    
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }
    
    public function getMimeType()
    {
        return $this->mimeType;
    }
    
    
    
    
    
    public function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    public function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'images/profile/'.$this->profile->getId();
    }
    
    
    
    
    

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->temp = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (isset($this->temp)) {
            unlink($this->temp);
        }
    }

    public function getAbsolutePath()
    {
        return null === $this->getPath()
            ? null
            : $this->getUploadRootDir().'/'.$this->id.'.'.$this->getPath();
    }

    

    /**
     * Get the image url
     *
     * @return null|string
     */
    public function getWebPath()
    {
        return null === $this->getPath()
            ? null
            : $this->getUploadDir().'/'.$this->id.'.'.$this->getPath();
    }
    
    public function delete()
    {
        unlink($this->getAbsolutePath());
    }
 
}