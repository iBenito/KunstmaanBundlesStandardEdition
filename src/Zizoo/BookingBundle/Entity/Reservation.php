<?php
namespace Zizoo\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Zizoo\BookingBundle\Entity\ReservationRepository")
 * @ORM\Table(name="reservation")
 * @ORM\HasLifecycleCallbacks()
 */
class Reservation
{
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
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;
    
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $renter;
    
    /**
     * @var float
     *
     * @ORM\Column(name="cost", type="decimal", precision=19, scale=4)
     */
    protected $cost;
    
    /**
     * @ORM\OneToMany(targetEntity="Zizoo\BookingBundle\Entity\Payment", mappedBy="reservation")
     */
    protected $payment;
    
    public function __construct()
    {
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
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
     * Set renter
     *
     * @param \Zizoo\UserBundle\Entity\User $renter
     * @return Reservation
     */
    public function setRenter(\Zizoo\UserBundle\Entity\User $renter = null)
    {
        $this->renter = $renter;
    
        return $this;
    }

    /**
     * Get renter
     *
     * @return \Zizoo\UserBundle\Entity\User 
     */
    public function getRenter()
    {
        return $this->renter;
    }

    /**
     * Set cost
     *
     * @param float $cost
     * @return Reservation
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
     * Add payment
     *
     * @param \Zizoo\BookingBundle\Entity\Payment $payment
     * @return Reservation
     */
    public function addPayment(\Zizoo\BookingBundle\Entity\Payment $payment)
    {
        $this->payment[] = $payment;
    
        return $this;
    }

    /**
     * Remove payment
     *
     * @param \Zizoo\BookingBundle\Entity\Payment $payment
     */
    public function removePayment(\Zizoo\BookingBundle\Entity\Payment $payment)
    {
        $this->payment->removeElement($payment);
    }

    /**
     * Get payment
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPayment()
    {
        return $this->payment;
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
}