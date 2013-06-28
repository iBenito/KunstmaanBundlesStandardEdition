<?php
namespace Zizoo\BookingBundle\Service;

use Zizoo\BookingBundle\Entity\Payment;
use Zizoo\BookingBundle\Entity\Booking;
use Zizoo\BookingBundle\Form\Model\Booking as BookingForm;
use Zizoo\BookingBundle\Exception\InvalidBookingException;
use Zizoo\BookingBundle\Entity\PaymentMethod;

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
    
    public static function priceToPayNow($price)
    {
        return number_format($price/2, 2);
    }
     
    private function makePayment(Booking $booking, $amount, $providerId, $flush=true, $provider=Payment::PROVIDER_BRAINTREE, $status=Payment::BRAINTREE_STATUS_INITIAL)
    {
        $payment = new Payment();
        $payment->setAmount((float)$amount);
        $payment->setProvider($provider);
        $payment->setProviderStatus($status);
        $payment->setBooking($booking);
        $payment->setProviderId($providerId);
        switch ($provider){
            case Payment::PROVIDER_BRAINTREE:
                $paymentMethod = $this->em->getRepository('ZizooBookingBundle:PaymentMethod')->findOneById('credit_card');
                break;
            default:
                $paymentMethod = $this->em->getRepository('ZizooBookingBundle:PaymentMethod')->findOneById('bank_transfer');
        }
        $payment->setPaymentMethod($paymentMethod);
        $booking->addPayment($payment);
        $this->em->persist($payment);
        if ($flush) $this->em->flush();
        return $payment;
    }
           
    private function storeBooking(User $user, Reservation $reservation, $intendedPrice, PaymentMethod $initialPaymentMethod, $crew, $flush=true){        
        $cut = $this->container->getParameter('zizoo_booking.cut_amount');
        $booking = new Booking();
        $booking->setCost($intendedPrice);
        $booking->setPayoutAmount($intendedPrice-($intendedPrice*$cut));
        $booking->setRenter($user);
        $booking->setReservation($reservation);
        $booking->setStatus('4');
        $booking->setInitialPaymentMethod($initialPaymentMethod);
        $booking->setCrew($crew);
        
        $reservation->setBooking($booking);
        
        $this->em->persist($booking);
        $this->em->persist($reservation);
        if ($flush) $this->em->flush();
        
        return $booking;
    }
    
    private function processBrainTreeSubmitForSettlementResult(Payment $payment, $result)
    {
        if ($result->success) {
            $payment->setProviderStatus(Payment::BRAINTREE_STATUS_SUBMITTED_FOR_SETTLEMENT);
            $payment->setUpdated(new \DateTime());
            $this->em->persist($payment);
        } else {
            $errors = array();
            foreach ($result->errors as $error){
                $errors[] = $error;
            }
            throw new InvalidBookingException(implode(';', $errors));
        }
    }
    
    private function brainTreeSubmitForSettlement(Payment $payment)
    {
        // Include Braintree API
        require_once $this->container->getParameter('braintree_path').'/lib/Braintree.php';
        \Braintree_Configuration::environment($this->container->getParameter('braintree_environment'));
        \Braintree_Configuration::merchantId($this->container->getParameter('braintree_merchant_id'));
        \Braintree_Configuration::publicKey($this->container->getParameter('braintree_public_key'));
        \Braintree_Configuration::privateKey($this->container->getParameter('braintree_private_key'));
        
        try {
            $result = \Braintree_Transaction::submitForSettlement($payment->getProviderId());
            return $this->processBrainTreeSubmitForSettlementResult($payment, $result);
        } catch (\Exception $e){
            throw new InvalidBookingException($e);
        }
        
    }
    
    public function submitForSettlement(Booking $booking, $flush)
    {
        $payment = $booking->getPayment()->first();
        if ($payment && $payment->getProvider()==Payment::PROVIDER_BRAINTREE){
            $this->brainTreeSubmitForSettlement($payment);
        }
        
        if ($flush) $this->em->flush();
        
        return $payment;
    }
    
    private function processBrainTreeVoidResult(Payment $payment, $result)
    {
        if ($result->success) {
            $payment->setProviderStatus(Payment::BRAINTREE_STATUS_VOID);
            $payment->setUpdated(new \DateTime());
            $this->em->persist($payment);
        } else {
            $errors = array();
            foreach ($result->errors as $error){
                $errors[] = $error;
            }
            throw new InvalidBookingException(implode(';', $errors));
        }
    }
    
    private function braintreeVoid(Payment $payment)
    {
        // Include Braintree API
        require_once $this->container->getParameter('braintree_path').'/lib/Braintree.php';
        \Braintree_Configuration::environment($this->container->getParameter('braintree_environment'));
        \Braintree_Configuration::merchantId($this->container->getParameter('braintree_merchant_id'));
        \Braintree_Configuration::publicKey($this->container->getParameter('braintree_public_key'));
        \Braintree_Configuration::privateKey($this->container->getParameter('braintree_private_key'));
        
        try {
            $result = \Braintree_Transaction::void($payment->getProviderId());
            return $this->processBrainTreeVoidResult($payment, $result);
        } catch (\Exception $e){
            throw new InvalidBookingException($e);
        }
        
    }
    
    public function void(Booking $booking, $flush)
    {
        $payment = $booking->getPayment()->first();
        if ($payment && $payment->getProvider()==Payment::PROVIDER_BRAINTREE){
            $this->braintreeVoid($payment);
        }
        
        if ($flush) $this->em->flush();
        
        return $payment;
    }
    
    public function processBraintreeResult($result, Boat $boat, $price, User $user, BookBoat $bookBoat, PaymentMethod $initialPaymentMethod)
    {
        $braintreeErrors = array();
        
        if ($result->success){
            // Transaction successful (payment could still be unauthorized)
            if ($result->transaction->status=='authorized'){
                // Payment authorized. 
                try {
                    // Start transaction on Zizoo
                    $reservationAgent   = $this->container->get('zizoo_reservation_reservation_agent');
                    $reservation        = $reservationAgent->makeReservation($boat, $bookBoat, $price, $user, false);
                    $booking            = $this->storeBooking($user, $reservation, $price, $initialPaymentMethod, $bookBoat->getCrew(), false);
                    $payment            = $this->makePayment($booking, (float)$result->transaction->amount, $result->transaction->id, false);
                    
                    // End transaction
                    $this->em->flush();
                    return $booking;
                    
                } catch (\Exception $e){
                    // Zizoo reservation could not be made
                    $braintreeErrors[] = $e->getMessage();
                    throw new InvalidBookingException(implode(';', $braintreeErrors));
                    // TODO: void transaction
                }
            } else {
                // Payment not authorized. TODO: handle!
                $braintreeErrors[] = $result->transaction->status . ': ' . $result->transaction->processorResponseText;
                throw new InvalidBookingException(implode(';', $braintreeErrors));
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
            throw new InvalidBookingException(implode(';', $braintreeErrors));
        }
    }
    
    private function braintreeMakeBooking(User $user, BookingForm $booking, $price, BookBoat $bookBoat, Boat $boat, PaymentMethod $initialPaymentMethod)
    {
        // Include Braintree API
        require_once $this->container->getParameter('braintree_path').'/lib/Braintree.php';
        \Braintree_Configuration::environment($this->container->getParameter('braintree_environment'));
        \Braintree_Configuration::merchantId($this->container->getParameter('braintree_merchant_id'));
        \Braintree_Configuration::publicKey($this->container->getParameter('braintree_public_key'));
        \Braintree_Configuration::privateKey($this->container->getParameter('braintree_private_key'));
        
        // Get Braintree customer
        $userService = $this->container->get('zizoo_user_user_service');
        $braintreeCustomer = $userService->getPaymentUser($user);
        if (!$braintreeCustomer){
            // TODO: handle?
            //throw new \Exception('Braintree customer not found for user with id: ' . $user->getId());
            throw new InvalidBookingException('There was an error with the payment provider when creating a customer');
            //return array('error' => array('There was an error with the payment provider when creating a customer'));
        }
        
        // Attempt to make Braintree transaction
        try {
            $result = \Braintree_Transaction::sale(array(
                'customerId'    => $user->getID(),
                'amount'        => $this->priceToPayNow($price),
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
            throw new InvalidBookingException($e);
            //return array('error' => array($e->getMessage()));
        }

        return $this->processBraintreeResult($result, $boat, $price, $user, $bookBoat, $initialPaymentMethod);
    }
    
    private function bankTransferMakeBooking(User $user, BookingForm $booking, $price, BookBoat $bookBoat, Boat $boat, PaymentMethod $initialPaymentMethod)
    {
        // Start transaction on Zizoo
        $reservationAgent   = $this->container->get('zizoo_reservation_reservation_agent');
        $reservation        = $reservationAgent->makeReservation($boat, $bookBoat, $price, $user, false);
        $booking            = $this->storeBooking($user, $reservation, $price, $initialPaymentMethod, $bookBoat->getCrew(), false);
        //$payment            = $this->makePayment($booking, $booking->getCost(), $result->transaction->id, false);
        
        // End transaction
        $this->em->flush();
        
        return $booking;
    }
    
    public function makeBooking(User $user, BookingForm $bookingForm, $price, BookBoat $bookBoat, Boat $boat)
    {
        $booking = null;
        $paymentMethod = $bookingForm->getPaymentMethod();
        if ($paymentMethod->getId()=='credit_card'){
            $booking =  $this->braintreeMakeBooking($user, $bookingForm, $price, $bookBoat, $boat, $paymentMethod);
        } else if ($paymentMethod->getId()=='bank_transfer'){
            $booking = $this->bankTransferMakeBooking($user, $bookingForm, $price, $bookBoat, $boat, $paymentMethod);
        } else {
            throw new InvalidBookingException("Payment method '".$paymentMethod->getName()."' not supported yet");
        }
        
        $composer       = $this->container->get('zizoo_message.composer');
        $sender         = $this->container->get('fos_message.sender');
        $messageTypeRepo = $this->container->get('doctrine.orm.entity_manager')->getRepository('ZizooMessageBundle:MessageType');
        
        $thread = $composer->newThread()
                            ->setSender($user)
                            ->addRecipient($boat->getCharter()->getAdminUser())
                            ->setSubject($bookingForm->getMessageToOwner()->getSubject())
                            ->setBody($bookingForm->getMessageToOwner()->getBody())
                            ->setBooking($booking);
        
        
        $message = $thread->getMessage()
                            ->setMessageType($messageTypeRepo->findOneById('inquiry'));
        
        
        $thread->setBooking($booking);
        
        $sender->send($message);
        
        return $booking;
    }
    
    public function PaymentStatusToString(Payment $payment)
    {
        switch ($payment->getProvider())
        {
            case Payment::PROVIDER_BRAINTREE:
                switch ($payment->getProviderStatus())
                {
                    case Payment::BRAINTREE_STATUS_INITIAL:
                        return 'Initial';
                        break;
                    case Payment::BRAINTREE_STATUS_SUBMITTED_FOR_SETTLEMENT:
                        return 'Pending';
                        break;
                    case Payment::BRAINTREE_STATUS_SETTLED:
                        return 'Completed';
                        break;
                    case Payment::BRAINTREE_STATUS_VOID:
                        return 'Void';
                        break;
                    default:
                        return $payment->getProvider().'-'.$payment->getProviderStatus();
                    
                }
                break;
            case Payment::PROVIDER_BANK_TRANSFER:
                switch ($payment->getProviderStatus())
                {
                    case Payment::BANK_TRANSFER_INITIAL:
                        return 'Pending';
                        break;
                    case Payment::BANK_TRANSFER_SETTLED:
                        return 'Complete';
                        break;
                    default:
                        return $payment->getProvider().'-'.$payment->getProviderStatus();
                }
                break;
            default:
                return $payment->getProvider();
                break;
        }
        
    }
    
    public function bookingPaidInFull(Booking $booking)
    {
        $payments = $booking->getPayment();
        $amountPaid = 0;
        foreach ($payments as $payment){
            switch ($payment->getProvider()){
                case Payment::PROVIDER_BRAINTREE:
                    if ($payment->getProviderStatus()!=Payment::BRAINTREE_STATUS_SETTLED) continue;
                    break;
                case Payment::PROVIDER_BANK_TRANSFER:
                    if ($payment->getProviderStatus()!=Payment::BANK_TRANSFER_SETTLED) continue;
                    break;
            }
            $amountPaid += $payment->getAmount();
        }
        return $amountPaid >= $booking->getCost();
    }
    
}
?>
