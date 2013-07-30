<?php

namespace Zizoo\BookingBundle\Entity;

use Zizoo\BaseBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="booking_payment")
 * @ORM\Entity(repositoryClass="Zizoo\BookingBundle\Entity\PaymentRepository")
 */
class Payment extends BaseEntity
{
    
   const PROVIDER_BRAINTREE                             = 0;
   const PROVIDER_BANK_TRANSFER                         = 1;
   
   const BRAINTREE_STATUS_INITIAL                       = 0;
   const BRAINTREE_STATUS_SUBMITTED_FOR_SETTLEMENT      = 1;
   const BRAINTREE_STATUS_SETTLED                       = 2;
   const BRAINTREE_STATUS_VOID                          = 3;
   
   const BANK_TRANSFER_INITIAL                          = 0;
   const BANK_TRANSFER_SETTLED                          = 1;
    
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
     * @ORM\Column(name="provider", type="integer", nullable=false)
     */
    private $provider;
    
    /**
     * @var int
     *
     * @ORM\Column(name="provider_status", type="integer", nullable=false)
     */
    private $providerStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="provider_id", type="text")
     */
    private $providerId;

    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\BookingBundle\Entity\PaymentMethod")
     * @ORM\JoinColumn(name="payment_method_id", referencedColumnName="id", nullable=false)
     */
    private $paymentMethod;
    
    /**
     * @var int
     *
     * @ORM\Column(name="settled", type="boolean", nullable=false)
     */
    private $settled;
    
    
    public function __construct() {
        $now = new \DateTime();
        $this->setCreated($now);
        $this->setUpdated($now);
        $this->settled = false;
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
     * Set provider
     *
     * @param integer $provider
     * @return Payment
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    
        return $this;
    }

    /**
     * Get provider
     *
     * @return integer 
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set providerStatus
     *
     * @param integer $providerStatus
     * @return Payment
     */
    public function setProviderStatus($providerStatus)
    {
        $this->providerStatus = $providerStatus;
    
        return $this;
    }

    /**
     * Get providerStatus
     *
     * @return integer 
     */
    public function getProviderStatus()
    {
        return $this->providerStatus;
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

    /**
     * Set providerId
     *
     * @param $providerId
     * @return Payment
     */
    public function setProviderId($providerId)
    {
        $this->providerId = $providerId;
    
        return $this;
    }

    /**
     * Get providerId
     *
     * @return 
     */
    public function getProviderId()
    {
        return $this->providerId;
    }
    
    /**
     * Set payment method
     *
     * @param \Zizoo\BookingBundle\Entity\PaymentMethod $paymentMethod
     * @return Payment
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }
    
    /**
     * Get payment method
     *
     * @return \Zizoo\BookingBundle\Entity\PaymentMethod 
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
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
}