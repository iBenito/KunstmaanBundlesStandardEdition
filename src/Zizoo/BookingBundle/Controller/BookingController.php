<?php

namespace Zizoo\BookingBundle\Controller;

use Zizoo\BookingBundle\Entity\Booking;
use Zizoo\BookingBundle\Entity\Payment;
use Zizoo\BookingBundle\Form\Model\Booking as BookingForm;
use Zizoo\BookingBundle\Form\Type\BookingType;
use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\BoatBundle\Form\Model\BookBoat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class BookingController extends Controller
{
    
    public function viewAllBookingsAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        $bookings = $em->getRepository('ZizooBookingBundle:Booking')->findByRenter($user->getId());
        return $this->render('ZizooBookingBundle:Booking:view_all_bookings.html.twig', array('bookings'       => $bookings));
    }
    
    public function viewBookingAction($id)
    {
        $user   = $this->getUser();
        $em     = $this->getDoctrine()->getEntityManager();
        $booking = $em->getRepository('ZizooBookingBundle:Booking')->findOneById($id);
        if (!$booking || $booking->getRenter()!=$user){
            return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard'));
        }
        
        return $this->render('ZizooBookingBundle:Booking:view_booking.html.twig', array('booking'       => $booking));
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
        $trans['credit_card'] = null;
        $request->request->set('transaction', $trans);
        $form->bind($request);
        return $form;
    }
    
    private function handleBookingError($bookingError)
    {
        if (is_array($bookingError)){
            if (array_key_exists('error', $bookingError)){
                return $bookingError['error'];
            } else {
                // Shouldn't happen, but handle anyway?
            }
        } else {
            // Shouldn't happen, but handle anyway?
        }
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
        // Include Braintree API
        require_once $this->container->getParameter('braintree_path').'/lib/Braintree.php';
        \Braintree_Configuration::environment($this->container->getParameter('braintree_environment'));
        \Braintree_Configuration::merchantId($this->container->getParameter('braintree_merchant_id'));
        \Braintree_Configuration::publicKey($this->container->getParameter('braintree_public_key'));
        \Braintree_Configuration::privateKey($this->container->getParameter('braintree_private_key'));
        
        // Get BookBoat from session
        $user           = $this->getUser();
        $session        = $request->getSession();
        $bookBoat       = $session->get('boat');
        $intendedPrice  = $session->get('price');
        if (!$bookBoat || !$intendedPrice){
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }
        
        // Get Boat entity
        $em = $this->getDoctrine()->getEntityManager();
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
            return $this->redirect($this->generateUrl('ZizooBoatBundle_show', array('id' => $boat->getId())));
        }
        
        // Calculate price
        $totalPrice = $reservationAgent->getTotalPrice($boat, $bookBoat->getReservationFrom(), $bookBoat->getReservationTo());
        
        // Get list of countries
        $countries = $em->getRepository('ZizooAddressBundle:Country')->findAll();
        
        // Generate Braintree Transparent Redirect (TR) data. Used when JavaScript not enabled.
        $trData = \Braintree_TransparentRedirect::transactionData(
            array(
                'transaction' => array(
                    'type'      => \Braintree_Transaction::SALE,
                    'amount'    => $bookingAgent->priceToPayNow($totalPrice),
                    'customerId'    => $user->getID(),
                    'options'       => array(
                        'storeInVaultOnSuccess'             => true,
                        'addBillingAddressToPaymentMethod'  => true
                    ),
                ),
                'redirectUrl' => $this->generateUrl('ZizooBookingBundle_book', array(), true)
            )
        );
        
        // Create form
        $bookingType = $this->container->get('zizoo_booking.booking_type');
        $form = $this->createForm($bookingType);
        $errors = array();
        
        $braintreeTransactionKind = $request->query->get('kind', null);
        
        if ($intendedPrice!=$totalPrice){
            throw new InvalidBookingException('Price mismatch: ' . $intendedPrice . ' != ' . $totalPrice);
        }
        
        if ($request->isMethod('POST')){
            $form->bindRequest($request);
            $bookingForm = $form->getData();
            
            if ($form->isValid()){
                
                try {
                    $booking = $bookingAgent->makeBooking($user, $bookingForm, $intendedPrice, $bookBoat, $boat);
                    // Reservation and payment successful
                    $session->remove('boat');
                    $session->remove('price');
                    return $this->redirect($this->generateUrl('ZizooBookingBundle_view_booking', array('id' => $booking->getID())));
                } catch (\Exception $e){
                    $errors[] = $e->getMessage();
                    $form = $this->clearCreditCardData($bookingType, $bookingForm, $request);
                }
            } else {
                // Form not valid
                // Clear credit card data
                $form = $this->clearCreditCardData($bookingType, $bookingForm, $request);
            }
        } else if ($braintreeTransactionKind == 'create_transaction'){
            $queryString = $_SERVER['QUERY_STRING'];
            try {
                $result = \Braintree_TransparentRedirect::confirm($queryString);
                // Reservation and payment successful
                $booking = $bookingAgent->processBraintreeResult($result, $boat, $user, $bookBoat);
                $session->remove('boat');
                $session->remove('price');
                return $this->redirect($this->generateUrl('ZizooBookingBundle_view_booking', array('id' => $booking->getID())));
            } catch (\Braintree_Exception $e){
                $errors[] = $e->getMessage();
                $form = $this->clearCreditCardData($bookingType, $bookingForm, $request);
            }
        }
        
        $clientSideBraintreeKey = $this->container->getParameter('braintree_client_side_key');
        return $this->render('ZizooBookingBundle:Booking:book.html.twig', array('boat'              => $boat,
                                                                                'book_boat'         => $bookBoat,
                                                                                'total_price'       => $totalPrice,
                                                                                'price_to_pay_now'  => $bookingAgent->priceToPayNow($totalPrice),
                                                                                'client_key'        => $clientSideBraintreeKey,
                                                                                'tr_data'           => $trData,
                                                                                'braintree_action'  => \Braintree_TransparentRedirect::url(),
                                                                                'countries'         => $countries,
                                                                                'user'              => $this->getUser(),
                                                                                'form'              => $form->createView(),
                                                                                'errors'            => $errors));
    }

}
