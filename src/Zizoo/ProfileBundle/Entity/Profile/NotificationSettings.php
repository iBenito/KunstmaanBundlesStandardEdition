<?php

namespace Zizoo\ProfileBundle\Entity\Profile;

use Zizoo\BaseBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationSettings
 *
 * @ORM\Table(name="profile_notification_settings")
 * @ORM\Entity
 */
class NotificationSettings extends BaseEntity
{

    /**
     * @var boolean
     *
     * @ORM\Column(name="message", type="boolean")
     */
    private $message;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enquiry", type="boolean")
     */
    private $enquiry;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="booking", type="boolean")
     */
    private $booking;

    /**
     * @var boolean
     *
     * @ORM\Column(name="booked", type="boolean")
     */
    private $booked;

    /**
     * @var boolean
     *
     * @ORM\Column(name="review", type="boolean")
     */
    private $review;

    /**
     * Set message
     *
     * @param boolean $message
     * @return NotificationSettings
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return boolean 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set booking
     *
     * @param boolean $booking
     * @return NotificationSettings
     */
    public function setBooking($booking)
    {
        $this->booking = $booking;
    
        return $this;
    }

    /**
     * Get booking
     *
     * @return boolean 
     */
    public function getBooking()
    {
        return $this->booking;
    }

    /**
     * Set booked
     *
     * @param boolean $booked
     * @return NotificationSettings
     */
    public function setBooked($booked)
    {
        $this->booked = $booked;
    
        return $this;
    }

    /**
     * Get booked
     *
     * @return boolean 
     */
    public function getBooked()
    {
        return $this->booked;
    }

    /**
     * Set enquiry
     *
     * @param boolean $enquiry
     * @return NotificationSettings
     */
    public function setEnquiry($enquiry)
    {
        $this->enquiry = $enquiry;
    
        return $this;
    }

    /**
     * Get enquiry
     *
     * @return boolean 
     */
    public function getEnquiry()
    {
        return $this->enquiry;
    }

    /**
     * Set review
     *
     * @param boolean $review
     * @return NotificationSettings
     */
    public function setReview($review)
    {
        $this->review = $review;
    
        return $this;
    }

    /**
     * Get review
     *
     * @return boolean 
     */
    public function getReview()
    {
        return $this->review;
    }
}