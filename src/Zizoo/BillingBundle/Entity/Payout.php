<?php

namespace Zizoo\BillingBundle\Entity;

use Zizoo\BaseBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="billing_payout")
 * @ORM\Entity(repositoryClass="Zizoo\BillingBundle\Entity\PayoutRepository")
 */
class Payout extends BaseEntity
{
    
   const PROVIDER_BANK_TRANSFER                         = 0;
   const PROVIDER_PAYPAL                                = 1;
   
   const BANK_TRANSFER_INITIAL                          = 0;
   const BANK_TRANSFER_SETTLED                          = 1;
   
   const PAYPAL_INITIAL                                 = 0;
   const PAYPAL_SETTLED                                 = 1;
       
    /**
     * @ORM\OneToMany(targetEntity="Zizoo\BookingBundle\Entity\Booking", mappedBy="payout")
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
     * @ORM\Column(name="provider_id", type="text", nullable=true)
     */
    private $providerId;

    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\BillingBundle\Entity\PayoutMethod")
     * @ORM\JoinColumn(name="payout_method_id", referencedColumnName="id", nullable=false)
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
        $this->booking = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add booking
     *
     * @param \Zizoo\BookingBundle\Entity\Booking $booking
     * @return Payment
     */
    public function addBooking(\Zizoo\BookingBundle\Entity\Booking $booking = null)
    {
        $this->booking[] = $booking;
    
        return $this;
    }

    public function removeBooking(\Zizoo\BookingBundle\Entity\Booking $booking = null)
    {
        $this->booking->remove($booking);
    
        return $this;
    }
    /**
     * Get booking
     *
     * @return \Doctrine\Common\Collections\Collection  
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