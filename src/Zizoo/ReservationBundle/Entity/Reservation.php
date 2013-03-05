<?php
namespace Zizoo\ReservationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Zizoo\ReservationBundle\Entity\ReservationRepository")
 * @ORM\Table(name="reservation")
 * @ORM\HasLifecycleCallbacks()
 */
class Reservation
{
    const STATUS_REQUESTED = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_EXPIRED = 3;
    const STATUS_DENIED = 4;
    
     /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\BoatBundle\Entity\Boat", inversedBy="reservation")
     * @ORM\JoinColumn(name="boat_id", referencedColumnName="id")
     */
    protected $boat;
        
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $guest;
    
    /**
     * @ORM\OneToOne(targetEntity="Zizoo\BookingBundle\Entity\Booking", mappedBy="reservation")
     */
    private $booking;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $check_in;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $check_out;

    /**
     * @ORM\Column(type="integer")
     */
    protected $nr_guests;
    
    /**
     * @ORM\Column(type="smallint")
     */
    private $status;
    
    /**
     * @ORM\OneToOne(targetEntity="Zizoo\AddressBundle\Entity\ReservationAddress", mappedBy="reservation")
     */
    protected $address;
    
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
        $this->check_in = $checkIn;
    
        return $this;
    }

    /**
     * Get check_in
     *
     * @return \DateTime 
     */
    public function getCheckIn()
    {
        return $this->check_in;
    }

    /**
     * Set check_out
     *
     * @param \DateTime $checkOut
     * @return Reservation
     */
    public function setCheckOut($checkOut)
    {
        $this->check_out = $checkOut;
    
        return $this;
    }

    /**
     * Get check_out
     *
     * @return \DateTime 
     */
    public function getCheckOut()
    {
        return $this->check_out;
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
     * Set created
     *
     * @param \DateTime $created
     * @return Reservation
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
     * @return Reservation
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
    
}