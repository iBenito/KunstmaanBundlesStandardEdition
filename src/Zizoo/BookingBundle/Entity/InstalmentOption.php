<?php

namespace Zizoo\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InstalmentOption
 *
 * @ORM\Table(name="booking_instalment_option", uniqueConstraints={@ORM\UniqueConstraint(name="idx", columns={"name"})})
 * @ORM\Entity(repositoryClass="Zizoo\BookingBundle\Entity\InstalmentOptionRepository")
 */
class InstalmentOption
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=255)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="pattern", type="text", nullable=true)
     */
    private $pattern;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="display_order", type="integer")
     */
    private $order;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;

    
    public function __construct($id=null, $name=null, $order=0) {
        $this->id       = $id;
        $this->name     = $name;
        $this->order    = $order;
        $this->enabled  = true;
    }
    
    /**
     * Set id
     *
     * @param string $id
     * @return InstalmentOption
     */
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
     * @return InstalmentOption
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
     * Set pattern
     *
     * @param string $pattern
     * @return InstalmentOption
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    
        return $this;
    }

    /**
     * Get pattern
     *
     * @return string 
     */
    public function getPattern()
    {
        return $this->pattern;
    }
    
    /**
     * Set order_num
     *
     * @param integer $orderNum
     * @return BoatType
     */
    public function setOrder($order)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get order_num
     *
     * @return integer 
     */
    public function getOrder()
    {
        return $this->order;
    }
    
    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return PaymentType
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }
    
    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    public function __toString()
    {
        return $this->name;
    }
}
