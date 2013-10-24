<?php
namespace Zizoo\BookingBundle\Service;

use Zizoo\BookingBundle\Entity\Booking;
use Zizoo\BookingBundle\Entity\Payment;
use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\UserBundle\Entity\User;

interface BookingAgentInterface 
{
    function makeBooking(Reservation $reservation, User $user, $cost, $crew);
    function createPaymentInstruction(Booking $booking, Payment $payment, $extendedData);
    function processPayment(Payment $payment);
    function reversePayment(Payment $payment);
    function addPayment(Booking $booking, $amount, $data);
    function processes($paymentMethod);
}
?>
