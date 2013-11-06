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
use Zizoo\BookingBundle\Entity\InstalmentOption;

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
    
    public function createPaymentsFromInstalmentOption(InstalmentOption $instalmentOption, \DateTime $now, \DateTime $checkIn, $total)
    {
        $payments = new \Doctrine\Common\Collections\ArrayCollection();
        $pattern = $instalmentOption->getPattern();
        $checkTotal = 0.0;
        
        $datePartPattern    = '(S|E)(\((-?\d+)\))*';
        $amountPartPattern  = '(\d+)';
        $instalmentPattern  = "$datePartPattern:$amountPartPattern";
        $matches = array();
        $numInstalmentOptions = preg_match_all("/$instalmentPattern+/", $pattern, $matches);
        
        for ($i=0; $i<$numInstalmentOptions; $i++){
            $optionPattern  = $matches[0][$i];
            $optionType     = $matches[1][$i];
            $optionDays     = $matches[3][$i];
            $optionAmount   = $matches[4][$i];
            
            $dateDue = null;
            
            if ($optionType=='S'){
                if ($optionDays!==''){
                    $dateDue = clone $now;
                    $dateDue->modify($optionDays.' days');
                }
            } else if ($optionType=='E'){
                $dateDue = clone $checkIn;
                if ($optionDays!=='') $dateDue->modify($optionDays.' days');
            } else {
                throw new \Exception('Invalid instalment option pattern: ' . $pattern . ', specifically: ' . $optionPattern);
            }
            
            $x = intval($optionAmount);
            if (!is_int($x)) throw new \Exception('Invalid instalment option pattern: ' . $pattern);
            $x /= 100;
            $checkTotal += floatval($x);
            $payment = new Payment();
            $payment->setAmount(round($x*$total, 2));
            $payment->setDateDue($dateDue);
            $payments->add($payment);
        }
        
//        $parts = explode(';', $pattern);
//        foreach ($parts as $part){
//            $p = explode(':', $part);
//            if (count($p)!=2) throw new \Exception('Invalid instalment option pattern: ' . $pattern);
//            switch ($p[0])
//            {
//                case 'S':
//                    $x = intval($p[1]);
//                    if (!is_int($x)) throw new \Exception('Invalid instalment option pattern: ' . $pattern);
//                    $x /= 100;
//                    $checkTotal += floatval($x);
//                    $payment = new Payment();
//                    $payment->setAmount(round($x*$total, 2));
//                    $payments->add($payment);
//                    break;
//                case (preg_match('/E*/', $p[0]) ? true : false) :
//                    $x = intval($p[1]);
//                    if (!is_int($x)) throw new \Exception('Invalid instalment option pattern: ' . $pattern);
//                    $x /= 100;
//                    $checkTotal += floatval($x);
//                    $payment = new Payment();
//                    $payment->setAmount(round($x*$total, 2));
//                    $payment->setDateDue($checkIn->modify('-5 days'));
//                    $payments->add($payment);
//                    break;
//                default:
//                    throw new \Exception('Invalid instalment option pattern: ' . $pattern);
//            }
//        }
        
        if ($checkTotal!==1.0) throw new \Exception('Invalid instalment option pattern: ' . $pattern);
        
        $roundedTotal = 0;
        foreach ($payments as $payment){
            $roundedTotal += $payment->getAmount();
        }
        $remainder = $roundedTotal - $total;
        if ($remainder!==0.0){
            $lastPayment = $payments->last();
            $lastPayment->setAmount($lastPayment->getAmount()-$remainder);
        }
        
        return $payments;
    }
    
    public function makeBooking(User $user, Boat $boat, \DateTime $reservationFrom, \DateTime $reservationTo, $price, $numGuests, $crew, $initialPaymentMethod, InstalmentOption $instalmentOption, $extraData=array())
    {
        $bookingsAllowed = $this->container->getParameter('zizoo_booking.allow_bookings') === true;
        if ($bookingsAllowed!==true){
            throw new InvalidBookingException('Bookings are currently not possible');
        }
        $plugin         = $this->getPlugin($initialPaymentMethod);
        $em                 = $this->container->get('doctrine.orm.entity_manager');
        $reservationAgent   = $this->container->get('zizoo_reservation_reservation_agent');
        
        // Create reservation (status: request)
        $reservation    = $reservationAgent->makeReservation($boat, $reservationFrom, $reservationTo, $numGuests, $price, $user);
        $booking        = $plugin->makeBooking($reservation, $user, $price, $crew);
        
        $payments       = $this->createPaymentsFromInstalmentOption($instalmentOption, new \DateTime(), clone $reservationFrom, $price);
        
        foreach ($payments as $payment){
            if ($payment->getDateDue()===null){
                $payment = $this->createPaymentInstruction($booking, $payment, $initialPaymentMethod, $extraData);
            }
            $booking->addPayment($payment);
            $payment->setBooking($booking);
            $em->persist($payment);
        }
        
        $autoAccept = false;
        if ($autoAccept===true){
            $this->acceptBooking($booking);
        }
        
        $em->flush();

        return $booking;
    }
    
    private function createPaymentInstruction(Booking $booking, Payment $payment, $paymentMethod, $extraData, $flush=false)
    {
        $plugin         = $this->getPlugin($paymentMethod);
        $extendedData   = $this->createExtendedData($extraData);
        $payment        = $plugin->createPaymentInstruction($booking, $payment, $extendedData);   
        
        if ($flush===true){
            $this->em->flush();
        }
        return $payment;
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
    
    public function addPayment(Booking $booking, $paymentMethod, $amount, $extraData, $flush=false)
    {
        $plugin         = $this->getPlugin($paymentMethod);
        $extendedData   = $this->createExtendedData($extraData);
        $payment        = $plugin->addPayment($booking, $amount, $extendedData);   
        
        if ($flush===true){
            $this->em->flush();
        }
        return $payment;
    }
        
    public function acceptBooking(Booking $booking, $flush=false)
    {
        $allPaymentsSuccessful = true;
        $successfulPayments = new \Doctrine\Common\Collections\ArrayCollection();
        $payments = $booking->getPayment();
        foreach ($payments as $payment){
            // Only handle new payments and payments due immediately
            if ($payment->getStatus()!=Payment::STATUS_NEW || $payment->getDateDue()!==null) continue;
            $instruction    = $payment->getPaymentInstruction();
            $plugin         = $this->getPlugin($instruction->getPaymentSystemName());
            $payment = $plugin->processPayment($payment);
            if ($payment->getStatus()==Payment::STATUS_SUCCESS) {
                $successfulPayments->add($payment);
            } else {
                $allPaymentsSuccessful = false;
            }   
        }
        if ($successfulPayments->count()==0) $allPaymentsSuccessful = false;
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
            if ($flush===true){
                $this->em->flush();
            }
        } else {
            throw new Zizoo\ReservationBundle\Exception\InvalidReservationException();
        }
        
        return $allPaymentsSuccessful;
    }
    
    public function denyBooking(Booking $booking, $flush=false)
    {
        $allReversePaymentsSuccessful = true;
        $successfulReversePayments = new \Doctrine\Common\Collections\ArrayCollection();
        $payments = $booking->getPayment();
        foreach ($payments as $payment){
            // Only handle new payments
            if ($payment->getStatus()!=Payment::STATUS_NEW || $payment->getDateDue()!==null) continue;
            $instruction    = $payment->getPaymentInstruction();
            $plugin         = $this->getPlugin($instruction->getPaymentSystemName());
            $payment = $plugin->reversePayment($payment);
            if ($payment->getStatus()==Payment::STATUS_SUCCESS) {
                $successfulReversePayments->add($payment);
            } else {
                $allReversePaymentsSuccessful = false;
            }   
        }
        if ($successfulReversePayments->count()==0) $allReversePaymentsSuccessful = false;
        
        $reservationAgent   = $this->container->get('zizoo_reservation_reservation_agent');
        $reservationAgent->denyReservation($booking->getReservation(), false);
        
        if ($flush===true){
            $this->em->flush();
        }
    }
        
    
}
?>
