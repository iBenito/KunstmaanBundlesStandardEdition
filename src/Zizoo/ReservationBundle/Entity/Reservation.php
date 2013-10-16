<?php
namespace Zizoo\ReservationBundle\Entity;

use Zizoo\BaseBundle\Entity\BaseEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Zizoo\ReservationBundle\Entity\ReservationRepository")
 * @ORM\Table(name="reservation")
 * @ORM\HasLifecycleCallbacks()
 */
class Reservation extends BaseEntity
{
    const STATUS_INITIAL    = 0;
    const STATUS_REQUESTED  = 1;
    const STATUS_ACCEPTED   = 2;
    const STATUS_EXPIRED    = 3;
    const STATUS_DENIED     = 4;
    const STATUS_SELF       = 5;
    const STATUS_HOLD       = 6;
    
    const NUM_STATUS        = 6;
    
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\BoatBundle\Entity\Boat", inversedBy="reservation")
     * @ORM\JoinColumn(name="boat_id", referencedColumnName="id")
     */
    protected $boat;
        
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\UserBundle\Entity\User", inversedBy="reservations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $guest;
    
    /**
     * @ORM\OneToOne(targetEntity="Zizoo\BookingBundle\Entity\Booking", mappedBy="reservation")
     */
    protected $booking;
    
    /**
     * @ORM\Column(type="datetime", name="check_in")
     */
    protected $checkIn;

    /**
     * @ORM\Column(type="datetime", name="check_out")
     */
    protected $checkOut;

    /**
     * @ORM\Column(type="integer")
     */
    protected $nr_guests;
    
    /**
     * @var float
     *
     * @ORM\Column(name="cost", type="decimal", precision=19, scale=4)
     */
    protected $cost;
    
    /**
     * @ORM\Column(type="smallint")
     */
    protected $status;
    
    /**
     * @ORM\OneToOne(targetEntity="Zizoo\AddressBundle\Entity\ReservationAddress", mappedBy="reservation", cascade={"remove"})
     */
    protected $address;
    
        /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $reason;
    
    /**
     * @ORM\Column(type="smallint", nullable=true, name="hours_to_respond")
     */
    protected $hoursToRespond;

    
    public function __construct()
    {
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
        $this->setStatus(self::STATUS_REQUESTED);
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
     * Set check_in
     *
     * @param \DateTime $checkIn
     * @return Reservation
     */
    public function setCheckIn($checkIn)
    {
        $this->checkIn = $checkIn;
    
        return $this;
    }

    /**
     * Get check_in
     *
     * @return \DateTime 
     */
    public function getCheckIn()
    {
        return $this->checkIn;
    }

    /**
     * Set check_out
     *
     * @param \DateTime $checkOut
     * @return Reservation
     */
    public function setCheckOut($checkOut)
    {
        $this->checkOut = $checkOut;
    
        return $this;
    }

    /**
     * Get check_out
     *
     * @return \DateTime 
     */
    public function getCheckOut()
    {
        return $this->checkOut;
    }

    /**
     * Set nr_guests
     *
     * @param integer $nrGuests
     * @return Reservation
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
     * Set cost
     *
     * @param float $cost
     * @return Booking
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
    
        return $this;
    }

    /**
     * Get cost
     *
     * @return float 
     */
    public function getCost()
    {
        return $this->cost;
    }
    
    /**
     * Set status
     *
     * @param integer $status
     * @return Reservation
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
     * Set boat
     *
     * @param \Zizoo\BoatBundle\Entity\Boat $boat
     * @return Reservation
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
     * Set guest
     *
     * @param \Zizoo\UserBundle\Entity\User $guest
     * @return Booking
     */
    public function setGuest(\Zizoo\UserBundle\Entity\User $guest = null)
    {
        $this->guest = $guest;
    
        return $this;
    }

    /**
     * Get guest
     *
     * @return \Zizoo\UserBundle\Entity\User 
     */
    public function getGuest()
    {
        return $this->guest;
    }
    
    /**
     * Set booking
     *
     * @param \Zizoo\BookingBundle\Entity\Booking $booking
     * @return Reservation
     */
    public function setBooking(\Zizoo\BookingBundle\Entity\Booking $booking = null)
    {
        $this->booking = $booking;
    
        return $this;
    }

    /**
     * Get booking
     *
     * @return \Zizoo\BookingBundle\Entity\Booking 
     */
    public function getBooking()
    {
        return $this->booking;
    }

    /**
     * Set address
     *
     * @param \Zizoo\AddressBundle\Entity\ReservationAddress $address
     * @return Reservation
     */
    public function setAddress(\Zizoo\AddressBundle\Entity\ReservationAddress $address = null)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return \Zizoo\AddressBundle\Entity\ReservationAddress 
     */
    public function getAddress()
    {
        return $this->address;
    }
    
    public function setReason($reason)
    {
        $this->reason = $reason;
        
        return $this;
    }
    
    public function getReason()
    {
        return $this->reason;
    }
    
    public function setHoursToRespond($hoursToRespond)
    {
        $this->hoursToRespond = $hoursToRespond;
        return $this;
    }
    
    public function getHoursToRespond()
    {
        return $this->hoursToRespond;
    }
    
    protected $test;
    
    public function getTest()
    {
        return $this->test;
    }
    
    public function setTest($test)
    {
        $this->test = $test;
        return $this;
    }
        
}