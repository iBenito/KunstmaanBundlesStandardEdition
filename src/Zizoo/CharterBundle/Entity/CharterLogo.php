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

    public function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'images/charter/'.$this->charter->getId();
    }
 
}