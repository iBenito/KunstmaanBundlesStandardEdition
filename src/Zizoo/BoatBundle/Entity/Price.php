<?php

namespace Zizoo\BoatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Availability
 * @ORM\Entity(repositoryClass="Zizoo\BoatBundle\Entity\PriceRepository")
 * @ORM\Table(name="boat_price")
 * @ORM\HasLifecycleCallbacks()
 */
class Price
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\BoatBundle\Entity\Boat", inversedBy="availability")
     * @ORM\JoinColumn(name="boat_id", referencedColumnName="id")
     */
    protected $boat;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $available_from;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $available_until;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=19, scale=4)
     */
    protected $price;

    
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
        $availableFrom->setTime(0,0,0);
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
        $availableUntil->setTime(23,59,59);
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

}