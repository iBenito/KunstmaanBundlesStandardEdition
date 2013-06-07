<?php

namespace Zizoo\BoatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoatType
 *
 * @ORM\Entity(repositoryClass="Zizoo\BoatBundle\Entity\OptionalExtraRepository")
 * @ORM\Table(name="boatoptional_extra", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"name"})})
 */
class OptionalExtra
{

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=255)
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="display_order", type="integer")
     */
    private $order;
    
    /**
     * @ORM\ManyToMany(targetEntity="Zizoo\BoatBundle\Entity\Boat", mappedBy="optionalExtra")
     */
    protected $boats;
    

    public function __construct($id=null, $name=null, $order=null) {
        $this->id       = $id;
        $this->name     = $name;
        $this->order    = $order;
    }

    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return BoatType
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return BoatType
     */
    public function setOrder($order)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get order
     *
     * @return integer 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Add boats
     *
     * @param \Zizoo\BoatBundle\Entity\Boat $boats
     * @return Equipment
     */
    public function addBoat(\Zizoo\BoatBundle\Entity\Boat $boats)
    {
        $this->boats[] = $boats;
    
        return $this;
    }

    /**
     * Remove boats
     *
     * @param \Zizoo\BoatBundle\Entity\Boat $boats
     */
    public function removeBoat(\Zizoo\BoatBundle\Entity\Boat $boats)
    {
        $this->boats->removeElement($boats);
    }

    /**
     * Get boats
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBoats()
    {
        return $this->boats;
    }
}