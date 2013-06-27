<?php
namespace Zizoo\CharterBundle\Entity;

use Zizoo\CharterBundle\Entity\Charter;

use Zizoo\MediaBundle\Entity\Media;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CharterLogo extends Media {
    
    /**
     * @ORM\OneToOne(targetEntity="Zizoo\CharterBundle\Entity\Charter", inversedBy="logo")
     * @ORM\JoinColumn(name="charter_id", referencedColumnName="id")
     */
    protected $charter;
    

    /**
     * Set charter
     *
     * @param \Zizoo\CharterBundle\Entity\Charter $charter
     * @return CharterLogo
     */
    public function setCharter(Charter $charter = null)
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
        return 'images/charter/'.$this->charter->getId();
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
 
}