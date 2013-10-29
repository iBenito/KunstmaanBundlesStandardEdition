<?php

namespace Zizoo\BookingBundle\Twig;

use Zizoo\BookingBundle\Entity\Payment;
use Zizoo\BookingBundle\Entity\Booking;
use Zizoo\BookingBundle\Service\BookingAgentController;


class BookingExtension extends \Twig_Extension
{
    protected $bookingAgent;
    
    public function __construct(BookingAgentController $bookingAgent) {
        $this->bookingAgent = $bookingAgent;
    }
    
    public function getFilters()
    {
        return array(
            'Booking_displayPrice'      => new \Twig_Filter_Method($this, 'displayPrice'),
            'Booking_amountPaid'        => new \Twig_Filter_Method($this, 'amountPaid'),
            'Booking_amountOutstanding' => new \Twig_Filter_Method($this, 'amountOutstanding'),
            'Booking_numberOfDays'      => new \Twig_Filter_Method($this, 'numberOfDays'),
            'Booking_simplePrice'       => new \Twig_Filter_Method($this, 'displaySimplePrice'),
            'Booking_priceToPayNow'     => new \Twig_Filter_Method($this, 'priceToPayNow'),
        );
    }
    
    public function displayPrice(Booking $booking){
        return number_format($booking->getCost(), 2);
    }
    
    public function displaySimplePrice(Booking $booking, $price){
        return number_format($price, 2);
    }
    
    private function getAmountPaid(Booking $booking){
        $amountPaid = 0;
        $payments = $booking->getPayment();
        foreach ($payments as $payment){
            if ($payment->getStatus()==Payment::STATUS_SUCCESS)
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
    
    public function priceToPayNow(Booking $booking, $fullAmountUpfront)
    {
        
    }
    
    public function getName()
    {
        return 'booking_extension';
    }
}
?>
