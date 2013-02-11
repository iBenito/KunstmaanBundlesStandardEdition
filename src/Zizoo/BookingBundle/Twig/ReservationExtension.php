<?php

namespace Zizoo\BookingBundle\Twig;

use Zizoo\BookingBundle\Entity\Payment;
use Zizoo\BookingBundle\Entity\Reservation;

class ReservationExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'Reservation_displayPrice'      => new \Twig_Filter_Method($this, 'displayPrice'),
            'Reservation_amountPaid'        => new \Twig_Filter_Method($this, 'amountPaid'),
            'Reservation_amountOutstanding' => new \Twig_Filter_Method($this, 'amountOutstanding'),
            'Reservation_numberOfDays'      => new \Twig_Filter_Method($this, 'numberOfDays'),
        );
    }
    
    public function displayPrice(Reservation $reservation){
        return number_format($reservation->getCost(), 2);
    }
    
    private function getAmountPaid(Reservation $reservation){
        $amountPaid = 0;
        $payments = $reservation->getPayment();
        foreach ($payments as $payment){
            if ($payment->getProviderStatus() >= Payment::BRAINTREE_STATUS_SUBMITTED_FOR_SETTLEMENT)
            {
                $amountPaid += $payment->getAmount();
            }
        }
        return $amountPaid;
    }
    
    public function amountPaid(Reservation $reservation){
        $amountPaid = $this->getAmountPaid($reservation);
        return number_format($amountPaid, 2);
    }
    
    public function amountOutstanding(Reservation $reservation){
        $amountOutstanding = $reservation->getCost() - $this->getAmountPaid($reservation);
        return number_format($amountOutstanding, 2);
    }

    public function numberOfDays(Reservation $reservation){
        $from   = $reservation->getCheckIn();
        $to     = $reservation->getCheckOut();
        if (!$from || !$to) return '...';
        $interval = $from->diff($to);
        return $interval->d;
    }
    
    public function getName()
    {
        return 'reservation_extension';
    }
}
?>
