<?php
namespace Zizoo\BoatBundle\Entity;

use Zizoo\BoatBundle\Entity\Boat;

use Zizoo\MediaBundle\Entity\Media;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class BoatImage extends Media {
    
    /**
     * @ORM\ManyToOne(targetEntity="\Zizoo\BoatBundle\Entity\Boat", inversedBy="image")
     * @ORM\JoinColumn(name="boat_id", referencedColumnName="id")
     */
    protected $boat;
    

    /**
     * Set boat
     *
     * @param \Zizoo\BoatBundle\Entity\Profile $boat
     * @return BoatImage
     */
    public function setBoat(Boat $boat = null)
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
        return 'images/boat/'.$this->boat->getId();
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