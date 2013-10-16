<?php
namespace Zizoo\BookingBundle\Service;

use Zizoo\BookingBundle\Entity\Booking;
use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\UserBundle\Entity\User;

interface BookingAgentInterface 
{
    function makeBooking(Reservation $reservation, User $user, $cost, $crew);
    function addPayment(Booking $booking, $amount);
//    function cancelBooking();
//    function cancelPayment();
    function processes($paymentMethod);
}
?>
