<?php

namespace Zizoo\BookingBundle\Controller;

use Zizoo\BookingBundle\Entity\Reservation;
use Zizoo\BookingBundle\Entity\Payment;
use Zizoo\BookingBundle\Form\Model\Booking;
use Zizoo\BookingBundle\Form\Type\BookingType;
use Zizoo\BoatBundle\Form\Model\BookBoat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class BookingController extends Controller
{
    
    public function viewAllReservationsAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        $reservations = $em->getRepository('ZizooBookingBundle:Reservation')->findByRenter($user->getId());
        return $this->render('ZizooBookingBundle:Booking:view_all_bookings.html.twig', array('reservations'       => $reservations));
    }
    
    public function viewReservationAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $reservation = $em->getRepository('ZizooBookingBundle:Reservation')->findOneById($id);
        if (!$reservation){
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }
        
        return $this->render('ZizooBookingBundle:Booking:view_booking.html.twig', array('reservation'       => $reservation));
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
        $form = $this->createForm($bookingType);
        $booking->setCreditCard(null);
        $form->setData($booking);
        $trans = $request->request->get('transaction');
        $trans['credit_card'] = null;
        $request->request->set('transaction', $trans);
        $form->bind($request);
        return $form;
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
        $user       = $this->getUser();
        $session    = $request->getSession();
        $bookBoat   = $session->get('boat');
        if (!$bookBoat){
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }
        
        // Get Boat entity
        $em = $this->getDoctrine()->getEntityManager();
        $boat = $em->getRepository('ZizooBoatBundle:Boat')->find($bookBoat->getBoatId());
        if (!$boat) {
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }  
        
        // Ensure boat is available for specified dates (from bookBoat)
        $bookingAgent = $this->get('booking_agent');
        $availability = $bookingAgent->getAvailability($boat, $bookBoat->getReservationFrom(), $bookBoat->getReservationTo());
        $validator = $this->get('zizoo_boat.book_boat_validator');
        $errors = $validator->validate($bookBoat, new \Zizoo\BoatBundle\Validator\Constraints\BookBoat());
        if ($errors || !$availability){
            return $this->redirect($this->generateUrl('ZizooBoatBundle_show', array('id' => $boat->getId())));
        }
        
        // Calculate price
        $price = $bookBoat->getPrice($availability);
        
        // Get list of countries
        $countries = $em->getRepository('ZizooAddressBundle:Country')->findAll();
        
        // Generate Braintree Transparent Redirect (TR) data. Used when JavaScript not enabled.
        $trData = \Braintree_TransparentRedirect::transactionData(
            array(
                'transaction' => array(
                    'type'      => \Braintree_Transaction::SALE,
                    'amount'    => $price,
                    'customerId'    => $user->getID(),
                    'options'       => array(
                        'storeInVaultOnSuccess'             => true,
                        'addBillingAddressToPaymentMethod'  => true
                    ),
                ),
                'redirectUrl' => $this->generateUrl('zizoo_book', array(), true)
            )
        );
        
        // Create form
        $bookingType = $this->container->get('zizoo_booking.booking_type');
        $form = $this->createForm($bookingType);
        $braintreeErrors = array();
        
        $braintreeTransactionKind = $request->query->get('kind', null);
        
        if ($request->isMethod('POST')){
            $form->bindRequest($request);
            $booking = $form->getData();
            
            if ($form->isValid()){
                $reservation = $bookingAgent->braintreeMakeReservation($user, $booking, $price, $bookBoat, $boat);
                if ($reservation instanceof Reservation){
                    // Reservation and payment successful
                    $session->remove('boat');
                    return $this->redirect($this->generateUrl('zizoo_view_booking', array('id' => $reservation->getID())));
                } else {
                    if (is_array($reservation)){
                        if (array_key_exists('error', $reservation)){
                            $braintreeErrors = $reservation['error'];
                        } else {
                            // Shouldn't happen, but handle anyway?
                        }
                    } else {
                        // Shouldn't happen, but handle anyway?
                    }
                    $form = $this->clearCreditCardData($bookingType, $booking, $request);
                }
            } else {
                // Form not valid
                // Clear credit card data
                $form = $this->clearCreditCardData($bookingType, $booking, $request);
            }
        } else if ($braintreeTransactionKind == 'create_transaction'){
            $queryString = $_SERVER['QUERY_STRING'];
            try {
                $result = \Braintree_TransparentRedirect::confirm($queryString);
                $reservation = $bookingAgent->processBraintreeResult($result, $boat, $user, $bookBoat);
                $booking = new Booking();
                if ($reservation instanceof Reservation){
                    // Reservation and payment successful
                    $session->remove('boat');
                    return $this->redirect($this->generateUrl('zizoo_view_booking', array('id' => $reservation->getID())));
                } else {
                    if (is_array($reservation)){
                        if (array_key_exists('error', $reservation)){
                            $braintreeErrors = $reservation['error'];
                        } else {
                            // Shouldn't happen, but handle anyway?
                        }
                    } else {
                        // Shouldn't happen, but handle anyway?
                    }
                    $form = $this->createForm($bookingType);
                }
            } catch (\Braintree_Exception $e){
                $braintreeErrors[] = 'Something went wrong with the payment provider';
            }
        }
        
        $clientSideBraintreeKey = $this->container->getParameter('braintree_client_side_key');
        return $this->render('ZizooBookingBundle:Booking:book.html.twig', array('boat'              => $boat,
                                                                                'book_boat'         => $bookBoat,
                                                                                'availability'      => $availability,
                                                                                'client_key'        => $clientSideBraintreeKey,
                                                                                'tr_data'           => $trData,
                                                                                'braintree_action'  => \Braintree_TransparentRedirect::url(),
                                                                                'countries'         => $countries,
                                                                                'user'              => $this->getUser(),
                                                                                'form'              => $form->createView(),
                                                                                'braintree_errors'  => $braintreeErrors));
    }

    
    public function bookTRConfirmAction(Request $request)
    {
        // Include Braintree API
        require_once $this->container->getParameter('braintree_path').'/lib/Braintree.php';
        \Braintree_Configuration::environment($this->container->getParameter('braintree_environment'));
        \Braintree_Configuration::merchantId($this->container->getParameter('braintree_merchant_id'));
        \Braintree_Configuration::publicKey($this->container->getParameter('braintree_public_key'));
        \Braintree_Configuration::privateKey($this->container->getParameter('braintree_private_key'));
        
        $queryString = $_SERVER['QUERY_STRING'];
        $result = \Braintree_TransparentRedirect::confirm($queryString);
        var_dump($result);
    }
}
