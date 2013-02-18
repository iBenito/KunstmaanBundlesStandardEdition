<?php
namespace Zizoo\BookingBundle\Service;

use Zizoo\BookingBundle\Entity\Payment;
use Zizoo\BookingBundle\Entity\Booking;
use Zizoo\BookingBundle\Form\Model\Booking as BookingForm;
use Zizoo\BookingBundle\Exception\InvalidBookingException;

use Zizoo\ReservationBundle\Entity\Reservation;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Form\Model\BookBoat;

use Zizoo\UserBundle\Entity\User;


class BookingAgent {
    
    private $em;
    private $messenger;
    private $container;
    
    public function __construct($em, $messenger, $container) {
        $this->em = $em;
        $this->messenger = $messenger;
        $this->container = $container;
    }
     
    private function makePayment(Booking $booking, $amount, $providerId, $flush=true, $provider=Payment::PROVIDER_BRAINTREE, $status=Payment::BRAINTREE_STATUS_INITIAL)
    {
        $payment = new Payment();
        $payment->setAmount((float)$amount);
        $payment->setProvider($provider);
        $payment->setProviderStatus($status);
        $payment->setBooking($booking);
        $payment->setProviderId($providerId);
        $booking->addPayment($payment);
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
    
    
    private function makeBooking(User $user, Reservation $reservation, $intendedPrice, $flush=true){        
        $booking = new Booking();
        $booking->setCost($intendedPrice);
        $booking->setRenter($user);
        $booking->setReservation($reservation);
        $booking->setStatus('4');
        
        $reservation->setBooking($booking);
        
        $this->em->persist($booking);
        $this->em->persist($reservation);
        if ($flush) $this->em->flush();
        
        return $booking;
    }
    
    
    public function processBraintreeResult($result, Boat $boat, User $user, BookBoat $bookBoat)
    {
        $braintreeErrors = array();
        
        if ($result->success){
            // Transaction successful (payment could still be unauthorized)
            if ($result->transaction->status=='authorized'){
                // Payment authorized. 
                try {
                    // Start transaction on Zizoo
                    $reservationAgent   = $this->container->get('zizoo_reservation_reservation_agent');
                    $reservation        = $reservationAgent->makeReservation($boat, $bookBoat, false);
                    $booking            = $this->makeBooking($user, $reservation, (float)$result->transaction->amount, false);
                    $payment            = $this->makePayment($booking, $booking->getCost(), $result->transaction->id, false);
                    
                    // End transaction
                    $this->em->flush();
                    
                    try {
                        // At this point the reservation, booking and booking_payment were made successfully on Zizoo
                        // Submit for settlement with Braintree
                        $submitResult = \Braintree_Transaction::submitForSettlement($result->transaction->id);
                        if ($submitResult->success){
                            // At this point submit for settlement was successful, so update payment status and flush()
                            $this->updatePayment($payment, Payment::BRAINTREE_STATUS_SUBMITTED_FOR_SETTLEMENT, true);
                            
                            return $booking;
                        } else {
                            $deepErrors = $submitResult->errors->deepAll();
                            foreach ($deepErrors as $deepError){
                                $braintreeErrors[] = $deepError->__get('message');
                            }
                            return array('error' => $braintreeErrors);
                        }

                        
                    } catch (\Exception $e){
                        // Could not submit for settlement
                        $braintreeErrors[] = $e->getMessage();
                        return array('error' => $braintreeErrors);
                    }
                    
                } catch (\Exception $e){
                    // Zizoo reservation could not be made
                    $braintreeErrors[] = $e->getMessage();
                    return array('error' => $braintreeErrors);
                    /**$braintreeErrors[] = $e->getMessage();
                    try {
                        // Void transaction
                        $voidResult = \Braintree_Transaction::void($result->transaction->id);

                        if ($voidResult->success){
                            // At this point transaction was voided successfully
                        } else {
                            // Transaction could not be voided - what to do?
                            $deepErrors = $voidResult->errors->deepAll();
                            foreach ($deepErrors as $deepError){
                                $braintreeErrors[] = $deepError->__get('message');
                            }
                        }

                        return array('error' => $braintreeErrors);
                    } catch (\Exception $e){
                        // Transaction could not be voided - what to do?
                        $braintreeErrors[] = $e->getMessage();
                        return array('error' => $braintreeErrors);
                    }*/
                }
            } else {
                // Payment not authorized. TODO: handle!
                $braintreeErrors[] = $result->transaction->status . ': ' . $result->transaction->processorResponseText;
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
    
    public function braintreeMakeBooking(User $user, BookingForm $booking, $price, BookBoat $bookBoat, Boat $boat)
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
        try {
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
        } catch (\Exception $e){
            return array('error' => array($e->getMessage()));
        }

        return $this->processBraintreeResult($result, $boat, $user, $bookBoat);
    }
    
}
?>
