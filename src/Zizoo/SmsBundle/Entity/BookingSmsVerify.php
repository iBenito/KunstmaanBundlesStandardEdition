<?php
namespace Zizoo\SmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class BookingSmsVerify extends SmsVerifyBase {

    /**
     * @ORM\OneToOne(targetEntity="Zizoo\BookingBundle\Entity\Booking", inversedBy="verification")
     */
    protected $booking;

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

}