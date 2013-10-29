<?php

namespace Zizoo\BookingBundle\Controller;

use Zizoo\BookingBundle\Entity\Booking;
use Zizoo\BookingBundle\Entity\Payment;
use Zizoo\BookingBundle\Form\Model\Booking as BookingForm;
use Zizoo\BookingBundle\Form\Model\Billing as BillingForm;
use Zizoo\BookingBundle\Form\Type\BookingType;
use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\BoatBundle\Form\Model\BookBoat;
use Zizoo\BaseBundle\Util\ZizooMath;
use Zizoo\BaseBundle\Util\Util;

use Zizoo\BookingBundle\Exception\InvalidBookingException;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class BookingController extends Controller
{
    
    public function viewAllBookingsAction()
    {
        $user       = $this->getUser();
        $em         = $this->getDoctrine()->getManager();
        $request    = $this->getRequest();
        $routes     = $request->query->get('routes');
        $bookings   = $em->getRepository('ZizooBookingBundle:Booking')->findByRenter($user->getId());
        return $this->render('ZizooBookingBundle:Booking:view_all_bookings.html.twig', array(   'bookings'      => $bookings,
                                                                                                'routes'        => $routes));
    }
    
    public function viewBookingAction($id)
    {
        $user       = $this->getUser();
        $em         = $this->getDoctrine()->getManager();
        $booking    = $em->getRepository('ZizooBookingBundle:Booking')->findOneById($id);
        $request    = $this->getRequest();
        $routes     = $request->query->get('routes');
        
        if (!$booking || ($booking->getRenter()!=$user)){
            return $this->redirect($this->generateUrl($routes['bookings_route']));
        }
        
        $reservation = $booking->getReservation();
        if (!$reservation){
            return $this->redirect($this->generateUrl($routes['bookings_route']));
        }
        
        $headers = array();
        $headers['x-zizoo-title'] = 'Booking <strong>' . $booking->getReference() . '</strong>';
        $response = new Response('', 200, $headers);
        
        $reservationAgent   = $this->get('zizoo_reservation_reservation_agent');
        $statusString = $reservationAgent->statusToString($reservation->getStatus());
        $hours = $reservationAgent->hoursToRespond($reservation);
        if ($reservation->getStatus() == Reservation::STATUS_REQUESTED && $hours){
            if ($hours >= 0){
                $statusString = "$statusString (expires in $hours hours)";
            } else {
                $statusString = "$statusString (expires soon)";
            }
        }
        
        return $this->render('ZizooBookingBundle:Booking:view_booking.html.twig', array(
            'booking'   => $booking, 
            'status'    => $statusString,
            'routes'    => $routes
        ), $response);
    }
    
    
    /**
     * Clear credit card data from form. This is required because the data is encrypted on the client-side, 
     * and we don't want to show the encrypted values to the user.
     * 
     * @param type $bookingType
     * @param type $booking
     * @param type $request
     * @return cleaned Form
     */
    private function clearCreditCardData($bookingType, $booking, $request)
    {
        $booking->setCreditCard(null);
        $form = $this->createForm($bookingType, $booking);
        
        //$form->setData($booking);
        $trans = $request->request->get('transaction');
        $trans['payment_method']['data_credit_card'] = null;
        $request->request->set('transaction', $trans);
        $form->bind($request);
        return $form;
    }
    
    private function createExtraData(BookingForm $bookingForm)
    {
        $paymentMethod      = $bookingForm->getPaymentMethod();
        $paymentMethodName  = $paymentMethod['method'];
        $paymentMethodData  = $paymentMethod['data_'.$paymentMethodName];
        
        $extraData = array(
            'billing'       => array(
                'firstName'             => $bookingForm->getBilling()->getFirstName(),
                'lastName'              => $bookingForm->getBilling()->getLastName(),
                'streetAddress'         => $bookingForm->getBilling()->getAddressLine1(),
                'extendedAddress'       => $bookingForm->getBilling()->getAddressLine2(),
                'locality'              => $bookingForm->getBilling()->getLocality(),
                'postalCode'            => $bookingForm->getBilling()->getPostcode(),
                'countryCodeAlpha2'     => $bookingForm->getBilling()->getCountry()->getIso()
            ),
        );
        
        switch ($paymentMethodName)
        {
            case 'credit_card':
                $extraData['creditCard'] = array(
                    'cardholderName'        => $paymentMethodData->getCardHolder(),
                    'number'                => $paymentMethodData->getCreditCardNumber(),
                    'expirationMonth'       => $paymentMethodData->getExpiryMonth(),
                    'expirationYear'        => $paymentMethodData->getExpiryYear(),
                    'cvv'                   => $paymentMethodData->getCVV()
                );
                    
        }
        
        return $extraData;
    }
    
    /**
     * If method is GET: display booking page (i.e. details of trip, credit card details, billing address).
     * If method is POST: make transaction with Braintree.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @throws \Exception
     */
    public function bookAction(Request $request)
    {
        // Get BookBoat from session
        $user           = $this->getUser();
        $session        = $request->getSession();
        $bookBoat       = $session->get('boat');
        if (!$bookBoat){
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }
        $intendedPrice  = $bookBoat->getTotal();
        if (!$intendedPrice){
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }
        
        // Get Boat entity
        $em = $this->getDoctrine()->getManager();
        $boat = $em->getRepository('ZizooBoatBundle:Boat')->find($bookBoat->getBoatId());
        if (!$boat) {
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }  
        
        // Ensure boat is available for specified dates (from bookBoat)
        $reservationAgent   = $this->get('zizoo_reservation_reservation_agent');
        $bookingAgent       = $this->get('zizoo_booking_booking_agent');
        $validator = $this->get('validator');
        $errors = $validator->validate($bookBoat);
        if ($errors && $errors->count()>0){
            return $this->redirect($this->generateUrl('ZizooBoatBundle_Boat_Show', array('id' => $boat->getId())));
        }
        
        // Calculate price
        $reservationRange = $bookBoat->getReservationRange();
        $totalPrice = $reservationAgent->getTotalPrice($boat, $reservationRange->getReservationFrom(), $reservationRange->getReservationTo(), $bookBoat->getCrew(), true);
        
        // Get list of countries
        $countries = $em->getRepository('ZizooAddressBundle:Country')->findAll();
                
        // Create form
        $bookingForm = new BookingForm();
        $billingForm = new BillingForm($user->getProfile());
        $bookingForm->setBilling($billingForm);
        $bookingType = $this->container->get('zizoo_booking.booking_type');
        $form = $this->createForm($bookingType, $bookingForm);
        $errors = array();
        
        if (!ZizooMath::floatcmp($intendedPrice, $totalPrice['total'])){
            throw new InvalidBookingException('Price mismatch: ' . $intendedPrice . ' != ' . $totalPrice['total']);
        }
        
        if ($request->isMethod('POST')){
            $form->bind($request);
            $bookingForm = $form->getData();
            
            if ($form->isValid()){
                
                try {
                    $reservationRange   = $bookBoat->getReservationRange();
                    $from               = $reservationRange->getReservationFrom();
                    $to                 = $reservationRange->getReservationTo();
                    $numGuests          = $bookBoat->getNumGuests();
                    $crew               = $bookBoat->getCrew();
                    $paymentMethod      = $bookingForm->getPaymentMethod();

                    $extraData = $this->createExtraData($bookingForm);
                    
                    $instalmentOption = $bookingForm->getInstalmentOption();
                    
                    $booking = $bookingAgent->makeBooking($user, $boat, $from, $to, $intendedPrice, $numGuests, $crew, $paymentMethod['method'], $instalmentOption, $extraData);
                    // Reservation and payment successful
                    $session->remove('boat');
                    $session->remove('price');
                    
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
                                        ->setMessageType($messageTypeRepo->findOneById('enquiry'));


                    $thread->setBooking($booking);

                    $sender->send($message);
                    
                    
                    return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard_ViewTrip', array('id' => $booking->getID())));
                } catch (\Exception $e){
                    $errors[] = $e->getMessage();
                    $form = $this->clearCreditCardData($bookingType, $bookingForm, $request);
                }
            } else {
                // Form not valid
                // Clear credit card data
                $form = $this->clearCreditCardData($bookingType, $bookingForm, $request);
            }
        }
        
        
        
        $cut = $this->container->getParameter('zizoo_booking.cut_amount');
     
        $braintreeConfig = $this->container->getParameter('zizoo_payment.braintree');
        $clientSideBraintreeKey = $braintreeConfig['client_side_key'];
        return $this->render('ZizooBookingBundle:Booking:book.html.twig', array('boat'              => $boat,
                                                                                'book_boat'         => $bookBoat,
                                                                                'total_price'       => $totalPrice,
                                                                                'cut'               => $cut,
                                                                                'price_to_pay_now'  => $intendedPrice,
                                                                                'client_key'        => $clientSideBraintreeKey,
                                                                                'braintree_action'  => \Braintree_TransparentRedirect::url(),
                                                                                'countries'         => $countries,
                                                                                'user'              => $this->getUser(),
                                                                                'form'              => $form->createView(),
                                                                                'errors'            => $errors));
    }

}
