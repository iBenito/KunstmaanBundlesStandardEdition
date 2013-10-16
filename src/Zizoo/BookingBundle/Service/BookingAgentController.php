<?php
namespace Zizoo\BookingBundle\Service;

use Zizoo\BookingBundle\Entity\Payment;
use Zizoo\BookingBundle\Entity\Booking;
use Zizoo\BookingBundle\Form\Model\Booking as BookingForm;
use Zizoo\BookingBundle\Exception\InvalidBookingException;
use Zizoo\BookingBundle\Entity\PaymentMethod;
use Zizoo\BookingBundle\Service\BookingAgentInterface;
use Zizoo\BookingBundle\Service\Exception\PluginNotFoundException;
use Zizoo\BookingBundle\Service\Exception\FunctionNotSupportedException;

use Zizoo\ReservationBundle\Entity\Reservation;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Form\Model\BookBoat;

use Zizoo\UserBundle\Entity\User;

use Symfony\Component\DependencyInjection\Container;
use JMS\Payment\CoreBundle\Entity\ExtendedData;

class BookingAgentController {
    
    private $em;
    private $messenger;
    private $container;
    private $plugins;
    
    public function __construct($em, $messenger, $container) {
        $this->em = $em;
        $this->messenger = $messenger;
        $this->container = $container;
        $this->plugins   = array();
    }
    
    public function addPlugin(BookingAgentInterface $plugin)
    {
        $this->plugins[] = $plugin;
    }
       
    private function getPlugin($paymentMethod)
    {
        foreach ($this->plugins as $plugin) {
            try {
                if ($plugin->processes($paymentMethod)) {
                    return $plugin;
                }
            } catch (FunctionNotSupportedException $e){
            }
        }

        throw new PluginNotFoundException(sprintf('There is no plugin that processes payments for "%s".', $paymentMethod));
    }
    
    public function makeBooking(User $user, Boat $boat, \DateTime $reservationFrom, \DateTime $reservationTo, $price, $numGuests, $crew, $paymentMethod, $extraData=array())
    {
        if (!$this->container->hasParameter('zizoo_booking.allow_bookings') || $this->container->getParameter('zizoo_booking.allow_bookings') !== true){
            throw new InvalidBookingException('Bookings are currently not possible');
        }
        $plugin         = $this->getPlugin($paymentMethod);
        $em                 = $this->container->get('doctrine.orm.entity_manager');
        $reservationAgent   = $this->container->get('zizoo_reservation_reservation_agent');
        
        
       
        // Create reservation (status: request)
        $reservation    = $reservationAgent->makeReservation($boat, $reservationFrom, $reservationTo, $numGuests, $price, $user);
        $booking        = $plugin->makeBooking($reservation, $user, $price, $crew);
        $payment        = $this->addPayment($booking, $paymentMethod, $price, $extraData);
        
        $autoAccept = false;
        if ($autoAccept===true){
            //$payment = $this->processPayment($payment, $price);
            $this->acceptBooking($booking);
        }
        
        $em->flush();

        return $booking;
    }
    
    private function createExtendedData($extraData)
    {
        $extendedData = new ExtendedData();
        foreach ($extraData as $k => $v) {
            if (is_array($v)){
                $extendedData->set($k, $this->createExtendedData($v), false, true);
            } else {
                $extendedData->set($k, $v, false, true);
            }
        }
        return $extendedData;
    }
    
    public function addPayment(Booking $booking, $paymentMethod, $amount, $extraData)
    {
        $plugin         = $this->getPlugin($paymentMethod);
        $extendedData   = $this->createExtendedData($extraData);
        $payment        = $plugin->addPayment($booking, $amount, $extendedData);   
        
        return $payment;
    }
    
//    public function processPayment(Payment $payment, $amount)
//    {
//        $plugin         = $this->getPlugin($payment->getPaymentInstruction()->getPaymentSystemName());
//        $payment        = $plugin->processPayment($payment, $amount);
//        
//        return $payment;
//    }
    
    public function acceptBooking(Booking $booking)
    {
        $allPaymentsSuccessful = true;
        $successfulPayments = new \Doctrine\Common\Collections\ArrayCollection();
        $payments = $booking->getPayment();
        foreach ($payments as $payment){
            // Only handle new paymetns
            if ($payment->getStatus()!=Payment::STATUS_NEW) continue;
            $instruction    = $payment->getPaymentInstruction();
            $plugin         = $this->getPlugin($instruction->getPaymentSystemName());
            $payment = $plugin->processPayment($payment);
            if ($payment->getStatus()==Payment::STATUS_SUCCESS) {
                $successfulPayments->add($payment);
            } else {
                $allPaymentsSuccessful = false;
            }
            
        }
        if ($allPaymentsSuccessful){
            try {
                $reservationAgent   = $this->container->get('zizoo_reservation_reservation_agent');
                $reservationAgent->acceptReservation($booking->getReservation(), true);
            } catch (Zizoo\ReservationBundle\Exception\InvalidReservationException $e){
                foreach ($successfulPayments as $successfulPayment){
                    $instruction    = $successfulPayment->getPaymentInstruction();
                    $plugin         = $this->getPlugin($instruction->getPaymentSystemName());
                    $payment        = $plugin->reversePayment($successfulPayment);
                }
            }
        }
        return $allPaymentsSuccessful;
    }
        
    
}
?>
