<?php

namespace Zizoo\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="reservation_payment")
 * @ORM\Entity
 */
class Payment
{
    
   const PROVIDER_BRAINTREE                             = 0;
   
   const BRAINTREE_STATUS_INITIAL                       = 0;
   const BRAINTREE_STATUS_SUBMITTED_FOR_SETTLEMENT      = 1;
   const BRAINTREE_STATUS_SETTLED                       = 2;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\BookingBundle\Entity\Reservation", inversedBy="payment")
     * @ORM\JoinColumn(name="reservation_id", referencedColumnName="id")
     */
    private $reservation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modified", type="datetime")
     */
    private $dateModified;

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

    public function __construct() {
        $now = new \DateTime();
        $this->setDateCreated($now);
        $this->setDateModified($now);
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
     * Set reservation
     *
     * @param \Zizoo\BookingBundle\Entity\Reservation $reservation
     * @return Payment
     */
    public function setReservation(\Zizoo\BookingBundle\Entity\Reservation $reservation)
    {
        $this->reservation = $reservation;
    
        return $this;
    }

    /**
     * Get reservation
     *
     * @return \Zizoo\BookingBundle\Entity\Reservation 
     */
    public function getReservation()
    {
        return $this->reservation;
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
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Payment
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     * @return Payment
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;
    
        return $this;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime 
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }
}