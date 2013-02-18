<?php

namespace Zizoo\BookingBundle\Twig;

use Zizoo\BookingBundle\Entity\Payment;
use Zizoo\BookingBundle\Entity\Booking;

class BookingExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'Booking_displayPrice'      => new \Twig_Filter_Method($this, 'displayPrice'),
            'Booking_amountPaid'        => new \Twig_Filter_Method($this, 'amountPaid'),
            'Booking_amountOutstanding' => new \Twig_Filter_Method($this, 'amountOutstanding'),
            'Booking_numberOfDays'      => new \Twig_Filter_Method($this, 'numberOfDays'),
        );
    }
    
    public function displayPrice(Booking $booking){
        return number_format($booking->getCost(), 2);
    }
    
    private function getAmountPaid(Booking $booking){
        $amountPaid = 0;
        $payments = $booking->getPayment();
        foreach ($payments as $payment){
            if ($payment->getProviderStatus() >= Payment::BRAINTREE_STATUS_SUBMITTED_FOR_SETTLEMENT)
            {
                $amountPaid += $payment->getAmount();
            }
        }
        return $amountPaid;
    }
    
    public function amountPaid(Booking $booking){
        $amountPaid = $this->getAmountPaid($booking);
        return number_format($amountPaid, 2);
    }
    
    public function amountOutstanding(Booking $booking){
        $amountOutstanding = $booking->getCost() - $this->getAmountPaid($booking);
        return number_format($amountOutstanding, 2);
    }

    public function numberOfDays(Booking $booking){
        $reservation = $booking->getReservation();
        $from   = $reservation->getCheckIn();
        $to     = $reservation->getCheckOut();
        if (!$from || !$to) return '...';
        $interval = $from->diff($to);
        return $interval->days;
    }
    
    public function getName()
    {
        return 'booking_extension';
    }
}
?>
