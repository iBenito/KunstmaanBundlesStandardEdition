<?php
// src/Zizoo/MediaBundle/Entity/Media.php
namespace Zizoo\MediaBundle\Entity;

use Zizoo\BaseBundle\Entity\BaseEntity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Media
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="media")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"profile_avatar" = "Zizoo\ProfileBundle\Entity\ProfileAvatar", "boat_image" = "Zizoo\BoatBundle\Entity\BoatImage", "charter_logo" = "Zizoo\CharterBundle\Entity\CharterLogo", "skill_license" = "Zizoo\CrewBundle\Entity\SkillLicense"})
 */
abstract class Media extends BaseEntity
{

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @ORM\Column(name="order_num", type="integer", nullable=false)
     */
    private $order;

    protected $x1;
    protected $y1;
    protected $x2;
    protected $y2;
    protected $w;
    protected $h;
    
    protected $file;
    

    public function __construct() 
    {
        parent::__construct();
        $this->order = 0;
    }
    
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
        
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }
    
    public function getPath()
    {
        return $this->path;
    }
        
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }
    
    public function getOrder()
    {
        return $this->order;
    }
    
    public function setX1($value)
    {
        $this->x1 = $value;
        return $this;
    }
    
    public function getX1()
    {
        return $this->x1;
    }
    
    public function setY1($value)
    {
        $this->y1 = $value;
        return $this;
    }
    
    public function getY1()
    {
        return $this->y1;
    }
    
    public function setX2($value)
    {
        $this->x2 = $value;
        return $this;
    }
    
    public function getX2()
    {
        return $this->x2;
    }
    
    public function setY2($value)
    {
        $this->y2 = $value;
        return $this;
    }
    
    public function getY2()
    {
        return $this->y2;
    }
    
    public function setW($value)
    {
        $this->w = $value;
        return $this;
    }
    
    public function getW()
    {
        return $this->w;
    }
    
    public function setH($value)
    {
        $this->h = $value;
        return $this;
    }
    
    public function getH()
    {
        return $this->h;
    }

    protected $temp;

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
    
    public function setFile(UploadedFile $file=null)
    {
        $this->file = $file;
        return $this;
    }
    
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @ORM\Column(name="mime_type", type="text", nullable=false)
     */
    protected $mimeType;

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

    public function getPathAndName()
    {
        return (null === $this->getPath() || null === $this->getId())
            ? null
            : $this->getId().'.'.$this->getPath();
    }

    public function getAbsolutePath()
    {
        return null === $this->getPathAndName()
            ? null
            : $this->getUploadRootDir().'/'.$this->getPathAndName();
    }

    /**
     * Get the image url
     *
     * @return null|string
     */
    public function getWebPath()
    {
        return null === $this->getPathAndName()
            ? null
            : $this->getUploadDir().'/'.$this->getPathAndName();
    }
}
?>
