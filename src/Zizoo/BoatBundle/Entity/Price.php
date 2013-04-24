<?php

namespace Zizoo\BoatBundle\Entity;

use Zizoo\BaseBundle\Entity\BaseEntity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Availability
 * @ORM\Entity(repositoryClass="Zizoo\BoatBundle\Entity\PriceRepository")
 * @ORM\Table(name="boat_price", uniqueConstraints={@ORM\UniqueConstraint(columns={"boat_id", "available"})})
 * @UniqueEntity(fields={"boat_id", "available"}, message="zizoo_boat.price_date_already_exists")
 * @ORM\HasLifecycleCallbacks()
 */
class Price extends BaseEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\BoatBundle\Entity\Boat", inversedBy="price")
     * @ORM\JoinColumn(name="boat_id", referencedColumnName="id")
     */
    protected $boat;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $available;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=19, scale=4)
     */
    protected $price;

   
    
    /**
     * Set available
     *
     * @param \DateTime $available
     * @return Price
     */
    public function setAvailable($available)
    {
        $available->setTime(0,0,0);
        $this->available = $available;
    
        return $this;
    }

    /**
     * Get available
     *
     * @return \DateTime 
     */
    public function getAvailable()
    {
        return $this->available;
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
    
    public function __toString()
    {
        return ''.$this->id.'';
    }

    
}