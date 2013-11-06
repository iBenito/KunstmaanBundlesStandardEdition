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
     * @ORM\ManyToOne(targetEntity="\Zizoo\BoatBundle\Entity\Boat", inversedBy="image", cascade={"persist"})
     * @ORM\JoinColumn(name="boat_id", referencedColumnName="id")
     */
    protected $boat;

    /**
     * Set boat
     *
     * @param \Zizoo\BoatBundle\Entity\Boat $boat
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

    public function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'images/boat/'.$this->boat->getId();
    }

}