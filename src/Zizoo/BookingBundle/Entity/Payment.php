<?php

namespace Zizoo\BookingBundle\Entity;

use Zizoo\BaseBundle\Entity\BaseEntity;

use JMS\Payment\CoreBundle\Entity\PaymentInstruction;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="booking_payment")
 * @ORM\Entity(repositoryClass="Zizoo\BookingBundle\Entity\PaymentRepository")
 */
class Payment extends BaseEntity
{
    const STATUS_NEW = 1;
    const STATUS_PENDING = 2;
    const STATUS_SUCCESS = 3;
    const STATUS_UNKNOWN = 4;
    
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\BookingBundle\Entity\Booking", inversedBy="payment")
     * @ORM\JoinColumn(name="booking_id", referencedColumnName="id")
     */
    private $booking;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="decimal", precision=19, scale=4)
     */
    private $amount;

    /**
     * @var int
     *
     * @ORM\Column(name="settled", type="integer", nullable=false)
     */
    private $status;
    
    
    /** @ORM\OneToOne(targetEntity="\JMS\Payment\CoreBundle\Entity\PaymentInstruction") */
    private $paymentInstruction;
    
    
    public function __construct() {
        $now = new \DateTime();
        $this->setCreated($now);
        $this->setUpdated($now);
        $this->status = Payment::STATUS_UNKNOWN;
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
     * Set amount
     *
     * @param float $amount
     * @return Payment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    
        return $this;
    }

    /**
     * Get amount
     *
     * @return float 
     */
    public function getAmount()
    {
        return $this->amount;
    }


    /**
     * Set booking
     *
     * @param \Zizoo\BookingBundle\Entity\Booking $booking
     * @return Payment
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

    public function setSettled($settled)
    {
        $this->settled = $settled;
        return $this;
    }
    
    public function getSettled()
    {
        return $this->settled;
    }
    
    public function getPaymentInstruction()
    {
        return $this->paymentInstruction;
    }

    public function setPaymentInstruction(PaymentInstruction $instruction)
    {
        $this->paymentInstruction = $instruction;
    }
}