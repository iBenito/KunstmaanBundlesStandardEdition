<?php

namespace Zizoo\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="booking_payment_method")
 * @ORM\Entity
 */
class PaymentMethod
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
     * @ORM\Column(name="name", type="text")
     */
    protected $name;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="display_order", type="integer")
     */
    protected $order;
    
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
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id
     * @return PaymentType
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Set name
     *
     * @param string $name
     * @return PaymentType
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Set order
     *
     * @param integer $order
     * @return PaymentType
     */
    public function setOrder($order)
    {
        $this->order = $order;
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

    public function __toString()
    {
        return $this->name;
    }
}