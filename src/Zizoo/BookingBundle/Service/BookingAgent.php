<?php
namespace Zizoo\BookingBundle\Service;

use Zizoo\BookingBundle\Entity\Payment;
use Zizoo\BookingBundle\Entity\Reservation;
use Zizoo\BookingBundle\Exception\InvalidReservationException;
use Zizoo\BookingBundle\Form\Model\Booking;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Form\Model\BookBoat;
use Zizoo\UserBundle\Entity\User;

use Zizoo\MessageBundle\Service\Messenger;
use Zizoo\MessageBundle\Entity\Message;
use Zizoo\MessageBundle\Entity\MessageRecipient;

use Zizoo\ProfileBundle\Entity\Profile;

use Doctrine\Common\Collections\ArrayCollection;



class BookingAgent {
    
    private $em;
    private $messenger;
    private $container;
    
    public function __construct($em, $messenger, $container) {
        $this->em = $em;
        $this->messenger = $messenger;
        $this->container = $container;
    }
    
    private function sendReservationMessage(User $user, Reservation $reservation){
        $composer = $this->container->get('zizoo_message.composer');

        $registerUser = $this->em->getRepository('ZizooUserBundle:User')->findOneByEmail($this->container->getParameter('email_register'));
        $threadTypeRepo = $this->em->getRepository('ZizooMessageBundle:ThreadType');
        if (!$registerUser) return false;
        
        $message = $composer->newThread()
                            ->setSender($registerUser)
                            ->addRecipient($user)
                            ->setSubject('Your booking')
                            ->setBody('This is the booking message')
                            ->setThreadType($threadTypeRepo->findOneByName('Booking'))
                            ->getMessage();
        
        $sender = $this->container->get('fos_message.sender');
        $sender->send($message);
        
        $this->messenger->sendNotificationBookingEmail($user, $reservation);
        
        return $message;
    }
    
    private function reservationExists($boat, $from, $to){
        $reservations = $boat->getReservation();
        foreach ($reservations as $reservation){
            $checkIn = $reservation->getCheckIn();
            $checkout = $reservation->getCheckOut();
            //(StartA <= EndB) and (EndA >= StartB)
            $inRange = ($from < $checkout) && ($to > $checkIn);
            //$inRange = !(($from < $checkIn && $to < $checkout) || ($from > $checkIn && $to > $checkout));
            if ($inRange) return true;
        }
        return false;
    }
    
    
    private function makeReservation(Boat $boat, User $user, BookBoat $bookBoat, $intendedPrice, $flush=true){
        $from = $bookBoat->getReservationFrom();
        $to   = $bookBoat->getReservationTo();
        $from->setTime(0,0,0);
        $to->setTime(23,59,59);
        if ($bookBoat->getNumGuests() > $boat->getNrGuests()) throw new InvalidReservationException('Too many guests: '.$bookBoat->getNumGuests().'>'.$boat->getNrGuests());
        $availabilities = $boat->getAvailability();
        foreach ($availabilities as $availability){
            if ($from >= $availability->getAvailableFrom() 
                    && $to <= $availability->getAvailableUntil())
            {
                if ($this->reservationExists($boat, $from, $to)){
                    throw new InvalidReservationException('Already booked');
                }
                $interval = $from->diff($to);
                $actualCost = $interval->d * $availability->getPrice();
                if (bccomp($actualCost, $intendedPrice, 3)!=0){
                //if ($actualCost!=$intendedPrice){
                    throw new InvalidReservationException('Price mismatch: '.$intendedPrice.'!='.$actualCost);
                }
                $reservation = new Reservation();
                $reservation->setCheckIn($from);
                $reservation->setCheckOut($to);
                $reservation->setNrGuests($bookBoat->getNumGuests());
                
                $reservation->setBoat($boat);
                $reservation->setStatus('4');
                
                $reservation->setRenter($user);
                $reservation->setCost($actualCost);
                                
                $this->em->persist($reservation);
                if ($flush) $this->em->flush();
                
                $this->sendReservationMessage($user, $reservation);
                return $reservation;
                break;
            }
        }
        throw new InvalidReservationException('Boat not available for '.$from->format('d/m/Y') . ' - ' . $to->format('d/m/Y'));
    }
    
    private function makePayment(Reservation $reservation, $amount, $flush=true, $provider=Payment::PROVIDER_BRAINTREE, $status=Payment::BRAINTREE_STATUS_INITIAL)
    {
        $payment = new Payment();
        $payment->setAmount((float)$amount);
        $payment->setProvider($provider);
        $payment->setProviderStatus($status);
        $payment->setReservation($reservation);
        $reservation->addPayment($payment);
        $this->em->persist($payment);
        if ($flush) $this->em->flush();
        return $payment;
    }
    
    private function updatePayment(Payment $payment, $status, $flush=true)
    {
        $payment->setProviderStatus($status);
        $this->em->persist($payment);
        if ($flush) $this->em->flush();
    }
    
    public function processBraintreeResult($result, Boat $boat, User $user, BookBoat $bookBoat)
    {
        if ($result->success){
            // Transaction successful (payment could still be unauthorized)
            if ($result->transaction->status=='authorized'){
                // Payment authorized. Attempt to make reservation with Zizoo (could fail).
                try {
                    $reservation = $this->makeReservation($boat, $user, $bookBoat, (float)$result->transaction->amount, false);
                    // At this point Zizoo reservation was successful, so submit for settlement with Braintree
                    $payment = $this->makePayment($reservation, (float)$result->transaction->amount, false);
                    try {
                        $result = \Braintree_Transaction::submitForSettlement($result->transaction->id);
                        // At this point submit for settlement was successful
                        $this->updatePayment($payment, Payment::BRAINTREE_STATUS_SUBMITTED_FOR_SETTLEMENT, false);
                        $this->em->flush();
                        return $reservation;
                    } catch (\Exception $e){
                        // Transaction couldn't be submitted for settlement.
                        $this->em->flush();
                        return array('error' => array('There was an error with the payment provider when submitting for settlement'));
                    }
                } catch (\Exception $e){
                    // Zizoo reservation could not be made
                    $braintreeErrors[] = $e->getMessage();
                    return array('error' => $braintreeErrors);
                }
            } else {
                // Payment not authorized. TODO: handle!
                return array('error' => $braintreeErrors);
            }


        } else {
            // Transaction not successful
            $deepErrors = $result->errors->deepAll();
            foreach ($deepErrors as $deepError){
                $braintreeErrors[] = $deepError->__get('message');
            }

            if (property_exists($result, 'transaction')){
                $braintreeErrors[] = $result->transaction->status . ': ' . $result->transaction->processorResponseText;
            }
            return array('error' => $braintreeErrors);
        }
    }
    
    public function braintreeMakeReservation(User $user, Booking $booking, $price, BookBoat $bookBoat, Boat $boat)
    {
        // Include Braintree API
        require_once $this->container->getParameter('braintree_path').'/lib/Braintree.php';
        \Braintree_Configuration::environment($this->container->getParameter('braintree_environment'));
        \Braintree_Configuration::merchantId($this->container->getParameter('braintree_merchant_id'));
        \Braintree_Configuration::publicKey($this->container->getParameter('braintree_public_key'));
        \Braintree_Configuration::privateKey($this->container->getParameter('braintree_private_key'));
        
        // Get Braintree customer
        $userService = $this->container->get('user_service');
        $braintreeCustomer = $userService->getPaymentUser($user);
        if (!$braintreeCustomer){
            // TODO: handle?
            //throw new \Exception('Braintree customer not found for user with id: ' . $user->getId());
            return array('error' => array('There was an error with the payment provider when creating a customer'));
        }

        // Attempt to make Braintree transaction
        $result = \Braintree_Transaction::sale(array(
            'customerId'    => $user->getID(),
            'amount'        => $price,
            'creditCard'    => array(
                'cardholderName'        => $booking->getCreditCard()->getCardHolder(),
                'number'                => $booking->getCreditCard()->getCreditCardNumber(),
                'expirationMonth'       => $booking->getCreditCard()->getExpiryMonth(),
                'expirationYear'        => $booking->getCreditCard()->getExpiryYear(),
                'cvv'                   => $booking->getCreditCard()->getCVV()
            ),
            'billing'       => array(
                'firstName'             => $booking->getBilling()->getFirstName(),
                'lastName'              => $booking->getBilling()->getLastName(),
                'streetAddress'         => $booking->getBilling()->getStreetAddress(),
                'extendedAddress'       => $booking->getBilling()->getExtendedAddress(),
                'locality'              => $booking->getBilling()->getLocality(),
                'region'                => $booking->getBilling()->getRegion(),
                'postalCode'            => $booking->getBilling()->getPostalCode(),
                'countryCodeAlpha2'     => $booking->getBilling()->getCountryCodeAlpha2()
            ),
            'options'       => array(
                'storeInVaultOnSuccess'             => true,
                'addBillingAddressToPaymentMethod'  => true
            )
        ));

        return $this->processBraintreeResult($result, $boat, $user, $bookBoat);
    }
    
    public function getAvailability(Boat $boat, $from, $to){
        if (!$from || !$to) return null;
        $from->setTime(0,0,0);
        $to->setTime(23,59,59);
        $availabilities = $boat->getAvailability();
        foreach ($availabilities as $availability){
            if ($from >= $availability->getAvailableFrom() 
                    && $to <= $availability->getAvailableUntil())
            {
                $reservationExists = $this->reservationExists($boat, $from, $to);
                if ($reservationExists){
                    return null;
                } else {
                    return $availability;
                }
            }
        }
        return null;
    }
    
    public function isAvailable(Boat $boat, $from, $to){
        $availability = $this->getAvailability($boat, $from, $to);
        return $availability!=null;
    }
    
}
?>
