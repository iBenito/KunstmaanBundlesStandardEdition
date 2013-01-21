<?php

namespace Zizoo\BoatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Availability
 * @ORM\Entity(repositoryClass="Zizoo\BoatBundle\Entity\AvailabilityRepository")
 * @ORM\Table(name="availability")
 * @ORM\HasLifecycleCallbacks()
 */
class Availability
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\BoatBundle\Entity\Boat", inversedBy="availability")
     * @ORM\JoinColumn(name="boat_id", referencedColumnName="id")
     */
    protected $boat;

    /**
     * @ORM\Column(type="datetime")
     */
    private $available_from;

    /**
     * @ORM\Column(type="datetime")
     */
    private $available_until;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;


    /**
     * @ORM\OneToOne(targetEntity="Zizoo\AddressBundle\Entity\AvailabilityAddress", mappedBy="availability")
     */
    protected $address;
    
    
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
     * Set available_from
     *
     * @param \DateTime $availableFrom
     * @return Availability
     */
    public function setAvailableFrom($availableFrom)
    {
        $this->available_from = $availableFrom;
    
        return $this;
    }

    /**
     * Get available_from
     *
     * @return \DateTime 
     */
    public function getAvailableFrom()
    {
        return $this->available_from;
    }

    /**
     * Set available_until
     *
     * @param \DateTime $availableUntil
     * @return Availability
     */
    public function setAvailableUntil($availableUntil)
    {
        $this->available_until = $availableUntil;
    
        return $this;
    }

    /**
     * Get available_until
     *
     * @return \DateTime 
     */
    public function getAvailableUntil()
    {
        return $this->available_until;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Availability
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set boat
     *
     * @param \Zizoo\BoatBundle\Entity\Boat $boat
     * @return Availability
     */
    public function setBoat(\Zizoo\BoatBundle\Entity\Boat $boat = null)
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
     * Set address
     *
     * @param \Zizoo\AddressBundle\Entity\AvailabilityAddress $address
     * @return Availability
     */
    public function setAddress(\Zizoo\AddressBundle\Entity\AvailabilityAddress $address = null)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return \Zizoo\AddressBundle\Entity\AvailabilityAddress 
     */
    public function getAddress()
    {
        return $this->address;
    }
}