<?php

namespace Zizoo\BoatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Zizoo\BoatBundle\Entity\BoatRepository")
 * @ORM\Table(name="boat")
 */
class Boat
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
        
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\UserBundle\Entity\User", inversedBy="boats")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $title;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\OneToOne(targetEntity="Zizoo\AddressBundle\Entity\BoatAddress", mappedBy="boat")
     */
    protected $address;

    /**
     * @ORM\Column(type="string")
     */
    protected $brand;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $model;

    /**
     * @ORM\Column(type="integer")
     */
    protected $length;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $cabins;
    
    /**
     * @ORM\Column(type="integer", nullable=TRUE)
     */
    protected $bathrooms;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $nr_guests;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $status;
       
    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="boat")
     */
    protected $image;
    
    /**
     * @ORM\OneToMany(targetEntity="Zizoo\ReservationBundle\Entity\Reservation", mappedBy="boat")
     */
    protected $reservation;
    
    /**
     * @ORM\Column(name="default_price", type="decimal", precision=19, scale=4)
     */
    protected $defaultPrice;

    /**
     * @ORM\OneToMany(targetEntity="Zizoo\BoatBundle\Entity\Price", mappedBy="boat")
     */
    protected $price;
    
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\BoatBundle\Entity\BoatType")
     * @ORM\JoinColumn(name="boat_type", referencedColumnName="id")
     */
    protected $boatType;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    public function __construct()
    {
        
        $this->image        = new ArrayCollection();
        $this->reservation  = new ArrayCollection();
        $this->created      = new \DateTime();
        $this->updated      = new \DateTime();
        $this->status       = 0;
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
     * Set title
     *
     * @param string $title
     * @return Boat
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Boat
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
     * Set description
     *
     * @param string $description
     * @return Boat
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set brand
     *
     * @param string $brand
     * @return Boat
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    
        return $this;
    }

    /**
     * Get brand
     *
     * @return string 
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set model
     *
     * @param string $model
     * @return Boat
     */
    public function setModel($model)
    {
        $this->model = $model;
    
        return $this;
    }

    /**
     * Get model
     *
     * @return string 
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set length
     *
     * @param integer $length
     * @return Boat
     */
    public function setLength($length)
    {
        $this->length = $length;
    
        return $this;
    }

    /**
     * Get length
     *
     * @return integer 
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set cabins
     *
     * @param integer $cabins
     * @return Boat
     */
    public function setCabins($cabins)
    {
        $this->cabins = $cabins;
    
        return $this;
    }

    /**
     * Get cabins
     *
     * @return integer 
     */
    public function getCabins()
    {
        return $this->cabins;
    }

    /**
     * Set bathrooms
     *
     * @param integer $bathrooms
     * @return Boat
     */
    public function setBathrooms($bathrooms)
    {
        $this->bathrooms = $bathrooms;
    
        return $this;
    }

    /**
     * Get bathrooms
     *
     * @return integer 
     */
    public function getBathrooms()
    {
        return $this->bathrooms;
    }

    /**
     * Set nr_guests
     *
     * @param integer $nrGuests
     * @return Boat
     */
    public function setNrGuests($nrGuests)
    {
        $this->nr_guests = $nrGuests;
    
        return $this;
    }

    /**
     * Get nr_guests
     *
     * @return integer 
     */
    public function getNrGuests()
    {
        return $this->nr_guests;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Boat
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Boat
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Boat
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set user
     *
     * @param \Zizoo\UserBundle\Entity\User $user
     * @return Boat
     */
    public function setUser(\Zizoo\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Zizoo\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set address
     *
     * @param \Zizoo\AddressBundle\Entity\BoatAddress $address
     * @return Boat
     */
    public function setAddress(\Zizoo\AddressBundle\Entity\BoatAddress $address = null)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return \Zizoo\AddressBundle\Entity\BoatAddress 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Add image
     *
     * @param \Zizoo\BoatBundle\Entity\Image $image
     * @return Boat
     */
    public function addImage(\Zizoo\BoatBundle\Entity\Image $image)
    {
        $this->image[] = $image;
    
        return $this;
    }

    /**
     * Remove image
     *
     * @param \Zizoo\BoatBundle\Entity\Image $image
     */
    public function removeImage(\Zizoo\BoatBundle\Entity\Image $image)
    {
        $this->image->removeElement($image);
    }

    /**
     * Get image
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add reservation
     *
     * @param \Zizoo\ReservationBundle\Entity\Reservation $reservation
     * @return Boat
     */
    public function addReservation(\Zizoo\ReservationBundle\Entity\Reservation $reservation)
    {
        $this->reservation[] = $reservation;
    
        return $this;
    }

    /**
     * Remove reservation
     *
     * @param \Zizoo\ReservationBundle\Entity\Reservation $reservation
     */
    public function removeReservation(\Zizoo\ReservationBundle\Entity\Reservation $reservation)
    {
        $this->reservation->removeElement($reservation);
    }

    /**
     * Get reservation
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     * Add price
     *
     * @param \Zizoo\BoatBundle\Entity\Price $price
     * @return Boat
     */
    public function addPrice(\Zizoo\BoatBundle\Entity\Price $price)
    {
        $this->price[] = $price;
    
        return $this;
    }

    /**
     * Remove price
     *
     * @param \Zizoo\BoatBundle\Entity\Price $price
     */
    public function removePrice(\Zizoo\BoatBundle\Entity\Price $price)
    {
        $this->price->removeElement($price);
    }

    /**
     * Get price
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set boatType
     *
     * @param \Zizoo\BoatBundle\Entity\BoatType $boatType
     * @return Boat
     */
    public function setBoatType(\Zizoo\BoatBundle\Entity\BoatType $boatType = null)
    {
        $this->boatType = $boatType;
    
        return $this;
    }

    /**
     * Get boatType
     *
     * @return \Zizoo\BoatBundle\Entity\BoatType 
     */
    public function getBoatType()
    {
        return $this->boatType;
    }

    /**
     * Set defaultPrice
     *
     * @param float $defaultPrice
     * @return Boat
     */
    public function setDefaultPrice($defaultPrice)
    {
        $this->defaultPrice = $defaultPrice;
    
        return $this;
    }

    /**
     * Get defaultPrice
     *
     * @return float 
     */
    public function getDefaultPrice()
    {
        return $this->defaultPrice;
    }
}