<?php
namespace Zizoo\BookingBundle\Entity;

use Zizoo\BaseBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Zizoo\BookingBundle\Entity\BookingRepository")
 * @ORM\Table(name="booking")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking extends BaseEntity
{

    /**
     * @ORM\OneToOne(targetEntity="Zizoo\ReservationBundle\Entity\Reservation", inversedBy="booking")
     * @ORM\JoinColumn(name="reservation_id", referencedColumnName="id", nullable=false)
     */
    protected $reservation;
        
    /**
     * @ORM\Column(type="smallint")
     */
    private $status;
    
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\UserBundle\Entity\User", inversedBy="bookings")
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
     * @ORM\OneToMany(targetEntity="Zizoo\BookingBundle\Entity\Payment", mappedBy="booking")
     */
    protected $payment;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\BookingBundle\Entity\PaymentMethod")
     * @ORM\JoinColumn(name="intial_payment_method_id", referencedColumnName="id", nullable=false)
     */
    private $initialPaymentMethod;
    
    /**
     * @var crew
     *
     * @ORM\Column(name="crew", type="boolean")
     */
    protected $crew;
    
    
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
     * Set status
     *
     * @param integer $status
     * @return Booking
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
     * Set renter
     *
     * @param \Zizoo\UserBundle\Entity\User $renter
     * @return Booking
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
     * Add payment
     *
     * @param \Zizoo\BookingBundle\Entity\Payment $payment
     * @return Booking
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
     * Set reservation
     *
     * @param \Zizoo\ReservationBundle\Entity\Reservation $reservation
     * @return Booking
     */
    public function setReservation(\Zizoo\ReservationBundle\Entity\Reservation $reservation = null)
    {
        $this->reservation = $reservation;
    
        return $this;
    }

    /**
     * Get reservation
     *
     * @return \Zizoo\ReservationBundle\Entity\Reservation 
     */
    public function getReservation()
    {
        return $this->reservation;
    }
    
    /**
     * Set initial payment method
     *
     * @param \Zizoo\BookingBundle\Entity\PaymentMethod $initialPaymentMethod
     * @return Booking
     */
    public function setInitialPaymentMethod(PaymentMethod $initialPaymentMethod)
    {
        $this->initialPaymentMethod = $initialPaymentMethod;
        return $this;
    }
    
    /**
     * Get initial payment method
     *
     * @return \Zizoo\BookingBundle\Entity\PaymentMethod 
     */
    public function getInitialPaymentMethod()
    {
        return $this->initialPaymentMethod;
    }
    
    public function setCrew($crew)
    {
        $this->crew = $crew;
        return $this;
    }
    
    public function getCrew()
    {
        return $this->crew;
    }
    
}