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

    const STATUS_OUTSTANDING    = 0;
    const STATUS_PAID           = 1;

    /**
     * @ORM\OneToOne(targetEntity="Zizoo\ReservationBundle\Entity\Reservation", inversedBy="booking")
     * @ORM\JoinColumn(name="reservation_id", referencedColumnName="id", nullable=false)
     */
    protected $reservation;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255)
     */
    private $reference;

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
     * @var float
     *
     * @ORM\Column(name="payout_amount", type="decimal", precision=19, scale=4)
     */
    protected $payout_amount;
    
    /**
     * @ORM\OneToMany(targetEntity="Zizoo\BookingBundle\Entity\Payment", mappedBy="booking")
     */
    protected $payment;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\BillingBundle\Entity\Payout", inversedBy="booking")
     * @ORM\JoinColumn(name="payout_id", referencedColumnName="id")
     */
    protected $payout;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="initial_payment_method", type="string", length=100)
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
        $this->setStatus(Booking::STATUS_OUTSTANDING);
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
     * Set Reference
     *
     * @param string $reference
     * @return Booking
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get Reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
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
     * Set cost
     *
     * @param float $cost
     * @return Booking
     */
    public function setPayoutAmount($payoutAmount)
    {
        $this->payout_amount = $payoutAmount;
    
        return $this;
    }

    /**
     * Get cost
     *
     * @return float 
     */
    public function getPayoutAmount()
    {
        return $this->payout_amount;
    }

    
    public function setPayout(\Zizoo\BillingBundle\Entity\Payout $payout=null)
    {
        $this->payout = $payout;
        return $this;
    }
    
    public function getPayout()
    {
        return $this->payout;
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
     * @param $initialPaymentMethod
     * @return Booking
     */
    public function setInitialPaymentMethod($initialPaymentMethod)
    {
        $this->initialPaymentMethod = $initialPaymentMethod;
        return $this;
    }
    
    /**
     * Get initial payment method
     *
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