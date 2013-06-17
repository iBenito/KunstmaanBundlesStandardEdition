<?php

namespace Zizoo\BoatBundle\Entity;

use Zizoo\BaseBundle\Entity\BaseEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Zizoo\BoatBundle\Entity\BoatRepository")
 * @ORM\Table(name="boat")
 * @ORM\HasLifecycleCallbacks()
 */
class Boat extends BaseEntity
{

   /**
    * @ORM\ManyToMany(targetEntity="\Zizoo\CharterBundle\Entity\Charter", mappedBy="boats")
    */
    protected $charter;
    
    /**
     * @ORM\Column(type="text", nullable=TRUE)
     */
    protected $title;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=TRUE)
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
     * @ORM\Column(name="default_price", type="decimal", precision=19, scale=4, nullable=true)
     */
    protected $defaultPrice;

    /**
     * @ORM\OneToMany(targetEntity="Zizoo\BoatBundle\Entity\Price", mappedBy="boat")
     */
    protected $price;
    
    /**
     * @ORM\Column(name="lowest_price", type="decimal", precision=19, scale=4, nullable=true)
     */
    protected $lowestPrice;
    
    /**
     * @ORM\Column(name="highest_price", type="decimal", precision=19, scale=4, nullable=true)
     */
    protected $highestPrice;
    
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\BoatBundle\Entity\BoatType")
     * @ORM\JoinColumn(name="boat_type", referencedColumnName="id")
     */
    protected $boatType;
    
    /**
     * @ORM\ManyToMany(targetEntity="Zizoo\BoatBundle\Entity\Equipment", inversedBy="boats")
     * @ORM\JoinTable(name="boat_equipment")
     **/
    protected $equipment;
    
    /**
     * @ORM\ManyToMany(targetEntity="Zizoo\BoatBundle\Entity\IncludedExtra", inversedBy="boats")
     * @ORM\JoinTable(name="boat_included_extras")
     **/
    protected $includedExtra;
    
    /**
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active;
    
    /**
     * @ORM\Column(name="min_days", type="integer", nullable=true)
     */
    protected $minimumDays;
    
    /**
     * @ORM\Column(name="crew_price", type="decimal", precision=19, scale=4, nullable=true)
     */
    protected $crewPrice;
    
    /**
     * @ORM\Column(name="num_crew", type="integer", nullable=true)
     */
    protected $numCrew;
    
    /**
     * @ORM\Column(name="crew_optional", type="boolean")
     */
    protected $crewOptional;

    
    public function __construct()
    {
        
        $this->image        = new ArrayCollection();
        $this->reservation  = new ArrayCollection();
        $this->created      = new \DateTime();
        $this->updated      = new \DateTime();
        $this->status       = 0;
        $this->active       = false;
        $this->crewOptional = false;
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
     * Set charter
     *
     * @param \Zizoo\CharterBundle\Entity\Charter $charter
     * @return Boat
     */
    public function setCharter(\Zizoo\CharterBundle\Entity\Charter $charter = null)
    {
        $this->charter = new \Doctrine\Common\Collections\ArrayCollection(array($charter));
    
        return $this;
    }

    /**
     * Get charter
     *
     * @return \Zizoo\CharterBundle\Entity\Charter 
     */
    public function getCharter()
    {
        return $this->charter->first();
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
    
    /**
     * Set lowestPrice
     *
     * @param float $lowestPrice
     * @return Boat
     */
    public function setLowestPrice($lowestPrice)
    {
        $this->lowestPrice = $lowestPrice;
    
        return $this;
    }

    /**
     * Get lowestPrice
     *
     * @return float 
     */
    public function getLowestPrice()
    {
        return $this->lowestPrice;
    }
    
    /**
     * Set highestPrice
     *
     * @param float $highestPrice
     * @return Boat
     */
    public function setHighestPrice($highestPrice)
    {
        $this->highestPrice = $highestPrice;
    
        return $this;
    }

    /**
     * Get highestPrice
     *
     * @return float 
     */
    public function getHighestPrice()
    {
        return $this->highestPrice;
    }
    
    /**
     * Add equipment
     *
     * @param \Zizoo\BoatBundle\Entity\Equipment $equipment
     * @return Boat
     */
    public function addEquipment(\Zizoo\BoatBundle\Entity\Equipment $equipment)
    {
        $this->equipment[] = $equipment;
    
        return $this;
    }

    /**
     * Remove equipment
     *
     * @param \Zizoo\BoatBundle\Entity\Equipment $equipment
     */
    public function removeEquipment(\Zizoo\BoatBundle\Entity\Equipment $equipment)
    {
        $this->equipment->removeElement($equipment);
    }

    /**
     * Get equipment
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEquipment()
    {
        return $this->equipment;
    }
    
    /**
     * Add included extra
     *
     * @param \Zizoo\BoatBundle\Entity\IncludedExtra $includedlExtra
     * @return Boat
     */
    public function addIncludedExtra(\Zizoo\BoatBundle\Entity\IncludedExtra $includedlExtra)
    {
        $this->includedExtra[] = $includedlExtra;
    
        return $this;
    }

    /**
     * Remove included extra
     *
     * @param \Zizoo\BoatBundle\Entity\IncludedExtra $equipment
     */
    public function removeIncludedExtra(\Zizoo\BoatBundle\Entity\IncludedExtra $includedExtra)
    {
        $this->includedExtra->removeElement($includedExtra);
    }

    /**
     * Get included extras
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIncludedExtra()
    {
        return $this->includedExtra;
    }
    
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }
    
    public function getActive()
    {
        return $this->active;
    }
    
    public function setMinimumDays($minimumDays)
    {
        $this->minimumDays = $minimumDays;
        return $this;
    }
    
    public function getMinimumDays()
    {
        return $this->minimumDays;
    }
    
    public function setCrewPrice($crewPrice)
    {
        $this->crewPrice = $crewPrice;
        return $this;
    }
    
    public function getCrewPrice()
    {
        return $this->crewPrice;
    }
    
    public function setNumCrew($numCrew)
    {
        $this->numCrew = $numCrew;
        return $this;
    }
    
    public function getNumCrew()
    {
        return $this->numCrew;
    }
    
    public function setCrewOptional($crewOptional)
    {
        $this->crewOptional = $crewOptional;
        return $this;
    }
    
    public function getCrewOptional()
    {
        return $this->crewOptional;
    }
    
    public function updateLowestAndHighestPrice()
    {
        $lowestPrice    = null;
        $highestPrice   = null;
        
        // Determine lowest and highest set price (i.e. for specific days)
        $allPrices = $this->getPrice();
        if ($allPrices){
            foreach ($allPrices as $price){
                if (!$lowestPrice) {
                    $lowestPrice = $price->getPrice();
                } else {
                    if ($price->getPrice() < $lowestPrice) $lowestPrice = $price->getPrice();
                }
                if (!$highestPrice) {
                    $highestPrice = $price->getPrice();
                } else {
                    if ($price->getPrice() > $highestPrice) $highestPrice = $price->getPrice();
                }
            }
        }
        $defaultPrice   = $this->getDefaultPrice();
        $crewPrice      = $this->getCrewPrice();
        
        // Lowest
        if ($lowestPrice && $defaultPrice){
            // If a set price exists and default price exists, determine the lower of the two
            $this->setLowestPrice($lowestPrice<$defaultPrice?$lowestPrice:$defaultPrice);
        } else if ($lowestPrice){
            // If a set price exists but not default price exists, the set price is the lowest price
            $this->setLowestPrice($lowestPrice);
        } else if ($defaultPrice){
            // If a default price exists but no set price exists, the default price is the lowest price
            $this->setLowestPrice($defaultPrice);
        } else {
            // Neither a set price nor a default price exist, so the lowest price cannot be determined
            $this->setLowestPrice(null);
        }
        
        // Highest
        if ($highestPrice && $defaultPrice){
            // If a set price exists and default price exists, determine the higher of the two
            $this->setHighestPrice($highestPrice>$defaultPrice?$highestPrice:$defaultPrice);
        } else if ($highestPrice){
            // If a set price exists but not default price exists, the set price is the highest price
            $this->setHighestPrice($highestPrice);
        } else if ($defaultPrice){
            // If a default price exists but no set price exists, the default price is the highest price
            $this->setHighestPrice($defaultPrice);
        } else {
            // Neither a set price nor a default price exist, so the highest price cannot be determined
            $this->setHighestPrice(null);
        }
        
        // Add crew price to already determined lowest and highest prices only if crew is included
        if ($crewPrice && $this->getCrewOptional()){
            if ($this->getLowestPrice()) $this->setLowestPrice($this->getLowestPrice() + $crewPrice);
            if ($this->getHighestPrice()) $this->setHighestPrice($this->getHighestPrice() + $crewPrice);
        }
    }
    
    /**
    * @ORM\preUpdate
    */
    public function preUpdate()
    {
        $this->updateLowestAndHighestPrice();
    }
    
    /**
    * @ORM\PrePersist
    */
    public function prePersist()
    {
        $this->updateLowestAndHighestPrice();
    }
    
}