<?php

namespace Zizoo\BaseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\DBAL\DBALException;

use Zizoo\BillingBundle\Entity\Payout;
use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\AddressBundle\Form\Model\SearchBoat;
use Zizoo\AddressBundle\Form\Type\SearchBoatType;

/**
 * Dashboard Controller for managing everything related to User account.
 *
 * @author Benito Gonzalez <vbenitogo@gmail.com>
 */
class DashboardController extends Controller {

    private $boatRoutes     = array('new_route'         => 'ZizooBaseBundle_Dashboard_CharterNewBoat',
                            'edit_route'                => 'ZizooBaseBundle_Dashboard_CharterEditBoat',
                            'details_route'             => 'ZizooBaseBundle_Dashboard_CharterEditDetailsBoat',
                            'photos_route'              => 'ZizooBaseBundle_Dashboard_CharterEditPhotosBoat',
                            'calendar_route'            => 'ZizooBaseBundle_Dashboard_CharterEditPriceBoat',
                            'confirm_route'             => 'ZizooBaseBundle_Dashboard_CharterConfirmPriceBoat',
                            'complete_route'            => 'ZizooBaseBundle_Dashboard_CharterBoats',
                            'delete_route'              => 'ZizooBaseBundle_Dashboard_CharterDeleteBoat'
                            );
    private $verifyRoutes   = array('verify_facebook_route'      => 'ZizooBaseBundle_Dashboard_VerifyFacebook',
                                    'unverify_facebook_route'    => 'ZizooBaseBundle_Dashboard_UnverifyFacebook');
    
    private function widgetCharterAction($charter, $route)
    {
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter_widget.html.twig', array(
            'charter'   => $charter,
            'route'     => $route,
        ));
    }
    
    private function widgetUserAction($user, $route)
    {
        $facebook       = $this->get('facebook');

        return $this->render('ZizooBaseBundle:Dashboard:user_widget.html.twig', array(
            'user'      => $user,
            'route'     => $route,
            'facebook'  => $facebook
        ));
    }
    
    /**
     * Displays Mini User Profile and Navigation
     * 
     * @return Response
     */
    public function widgetAction($route)
    {
        $user       = $this->getUser();
        $url        = $this->generateUrl($route, array('id' => 0));

        $pattern = '/^\charter\/|^\/app_dev\.php\/charter\//';
        $isCharterRoute = preg_match($pattern, $url);
        
        if ($user->getCharter() && $isCharterRoute){
            return $this->widgetCharterAction($user->getCharter(), $route);
        } else {
            return $this->widgetUserAction($user, $route);
        }
    }
    
    
    /**
     * Display Charter Dashboard
     * 
     * @return Response
     */
    public function indexCharterAction()
    {
        $user       = $this->getUser();
        $charter    = $user->getCharter();
        
        $messageProvider    = $this->container->get('fos_message.provider');
        $unreadMessages     = $messageProvider->getNbUnreadMessages();

        $reservationRepository  = $this->getDoctrine()->getRepository('ZizooReservationBundle:Reservation');
        $reservationRequests    = $reservationRepository->getReservationRequests($charter);
        $upcomingWeekRequests   = $reservationRepository->getUpcomingWeekReservations($charter);
        $acceptedRequests       = $reservationRepository->getAcceptedReservations($charter);

        $boatRepository     = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat');
        $activeListings     = $boatRepository->getNumberOfCharterBoats($charter, TRUE);
        $incompleteListings = $boatRepository->getNumberOfCharterBoats($charter, FALSE);
        $hiddenListings     = $boatRepository->getNumberOfCharterBoats($charter, FALSE, TRUE);

        $bookingRepository      = $this->getDoctrine()->getRepository('ZizooBookingBundle:Booking');
        $outstandingPayments    = $bookingRepository->getOutstandingBookings($charter);
        $receivedPayments       = $bookingRepository->getPaidBookings($charter);

        $payoutRepository   = $this->getDoctrine()->getRepository('ZizooBillingBundle:Payout');
        $settledPayouts     = $payoutRepository->getSettledPayouts($charter);
        $form = $this->createForm(new SearchBoatType($this->container), new SearchBoat());
        
        return $this->render('ZizooBaseBundle:Dashboard:Charter/index.html.twig', array(
            'unreadMessages'        => $unreadMessages,
            'reservationRequests'   => count($reservationRequests),
            'upcomingRequests'      => count($upcomingWeekRequests),
            'acceptedRequests'      => count($acceptedRequests),
            'activeListings'        => $activeListings,
            'incompleteListings'    => $incompleteListings,
            'hiddenListings'        => $hiddenListings,
            'outstandingPayments'   => count($outstandingPayments),
            'receivedPayments'      => count($receivedPayments),
            'settledPayouts'        => $settledPayouts,
            'searchForm'            => $form->createView()
        ));
    }
    
    /**
     * Display User Dashboard
     * 
     * @return Response
     */
    public function indexUserAction()
    {
        $user               = $this->getUser();
        $reservationsMade   = $user->getReservations();
        $bookingsMade       = $user->getBookings();
        
        $messageProvider    = $this->container->get('fos_message.provider');
        $unreadMessages     = $messageProvider->getNbUnreadMessages();

        $form = $this->createForm(new SearchBoatType($this->container), new SearchBoat());
        return $this->render('ZizooBaseBundle:Dashboard:index.html.twig', array(
            'reservations'      => $reservationsMade,
            'bookings'          => $bookingsMade,
            'unreadMessages'    => $unreadMessages,
            'searchForm'        => $form->createView()
        ));
    }
    

    /**
     * Display User Inbox
     * 
     * @return Response
     */
    public function inboxAction()
    {
        $request    = $this->getRequest();
        $response   = $this->forward('ZizooMessageBundle:Message:inbox', array(), array('inbox_url'     => 'ZizooBaseBundle_Dashboard_Inbox',
                                                                                        'sent_url'      => 'ZizooBaseBundle_Dashboard_Sent'));
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
        
        return $this->render('ZizooBaseBundle:Dashboard:inbox.html.twig', array(
            'username'  => $this->getUser()->getUsername(),
            'response'  => $response->getContent()
        ));
    }
    
    /**
     * Display User outbox
     *
     * @return Response
     */
    public function sentAction()
    {
        $request    = $this->getRequest();
        $response   = $this->forward('ZizooMessageBundle:Message:sent', array(), array('inbox_url' => 'ZizooBaseBundle_Dashboard_Inbox',
                                                                                        'sent_url'  => 'ZizooBaseBundle_Dashboard_Sent'));
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
        
        
        $user = $this->getUser();
        $charter = $user->getCharter();
        return $this->render('ZizooBaseBundle:Dashboard:outbox.html.twig', array(
            'id'        => $charter->getId(),
            'response'  => $response->getContent()
        ));
    }

    /**
     * Display User Profile
     *
     * @return Response
     */
    public function profileAction()
    {
        $request    = $this->getRequest();
        $response   = $this->forward('ZizooProfileBundle:Profile:edit', array(), array('redirect_route'    => $request->get('_route')));
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
        
        return $this->render('ZizooBaseBundle:Dashboard:profile.html.twig', array(
            'username'  => $this->getUser()->getUsername(),
            'response'  => $response->getContent()
        ));
    }
    
    /**
     * Display User Account Settings
     *
     * @return Response
     */
    public function accountSettingsAction()
    {
        $request    = $this->getRequest();
        $response   = $this->forward('ZizooUserBundle:User:accountSettings', array(), array('redirect_route'    => $request->get('_route')));
        
        if ($response->isRedirect()){
           return $this->redirect($response->headers->get('Location'));
        }
        
        return $this->render('ZizooBaseBundle:Dashboard:account_settings.html.twig', array(
            'response'  => $response->getContent()
        ));
    }
    
    
    /**
     * Display User Skills
     *
     * @return Response
     */
    public function skillsAction()
    {
        return $this->render('ZizooBaseBundle:Dashboard:skills.html.twig', array(
        ));
    }

    /**
     * Display User Bookings
     *
     * @return Response
     */
    public function tripsAction()
    {
        $request    = $this->getRequest();
        $response   = $this->forward('ZizooBookingBundle:Booking:viewAllBookings', array(), array('redirect_route'  => $request->get('_route')));
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
        
        $user = $this->getUser();

        $bookings = $user->getBookings();

        return $this->render('ZizooBaseBundle:Dashboard:trips.html.twig', array(
            'bookings' => $bookings
        ));
    }
    
    /**
     * 
     *
     * @return Response
     */
    public function verifyFacebookAction()
    {
        $request    = $this->getRequest();
        
        $params = $request->query->all();
        $params['routes'] = $this->verifyRoutes;
        
        $response   = $this->forward('ZizooUserBundle:Verification:verifyFacebook', array(), $params);
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
        
        return $this->render('ZizooBaseBundle:Dashboard:verify.html.twig', array(
            'response'  => $response->getContent()
        ));
    }
    
    /**
     * 
     *
     * @return Response
     */
    public function unverifyFacebookAction()
    {
        $request    = $this->getRequest();
        
        $params = $request->query->all();
        $params['routes'] = $this->verifyRoutes;
        
        $response   = $this->forward('ZizooUserBundle:Verification:unverifyFacebook', array(), $params);
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
        
        return $this->render('ZizooBaseBundle:Dashboard:verify.html.twig', array(
            'response'  => $response->getContent()
        ));
    }
    
    /**
     * Display Charter Profile
     *
     * @return Response
     */
    public function charterProfileAction()
    {
        $request    = $this->getRequest();
        $response   = $this->forward('ZizooCharterBundle:Charter:profile', array(), array(  'redirect_route'        => $request->get('_route'),
                                                                                            'unauthorized_route'    => 'ZizooBaseBundle_Dashboard_CharterDashboard'));
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
        
        $user = $this->getUser();
        $charter = $user->getCharter();
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter.html.twig', array(
            'title'     => 'My Charter Profile',
            'current'   => 'profile',
            'id'        => $charter->getId(),
            'response'  => $response->getContent()
        ));
    }
    
    /**
     * Display Charter inbox
     *
     * @return Response
     */
    public function charterInboxAction()
    {
        $request    = $this->getRequest();
        $response   = $this->forward('ZizooMessageBundle:Message:inbox', array(), array('inbox_url' => 'ZizooBaseBundle_Dashboard_CharterInbox',
                                                                                        'sent_url'  => 'ZizooBaseBundle_Dashboard_CharterSent'));
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
        
        $user = $this->getUser();
        $charter = $user->getCharter();
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter.html.twig', array(
            'title'     => 'My Inbox',
            'current'   => 'inbox',
            'id'        => $charter->getId(),
            'response'  => $response->getContent()
        ));
    }
    
    /**
     * Display Charter outbox
     *
     * @return Response
     */
    public function charterSentAction()
    {
        $request    = $this->getRequest();
        $response   = $this->forward('ZizooMessageBundle:Message:sent', array(), array('inbox_url' => 'ZizooBaseBundle_Dashboard_CharterInbox',
                                                                                        'sent_url'  => 'ZizooBaseBundle_Dashboard_CharterSent'));
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
        
        $user = $this->getUser();
        $charter = $user->getCharter();
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter.html.twig', array(
            'title'     => 'My Sent Messages',
            'current'   => 'inbox',
            'id'        => $charter->getId(),
            'response'  => $response->getContent()
        ));
    }

    /**
     * Display Charter Bookings
     *
     * @return Response
     */
    public function charterBookingsAction()
    {
        $request    = $this->getRequest();
        $response   = $this->forward('ZizooCharterBundle:Charter:bookings', array(), array('redirect_route' => $request->get('_route')));
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
        
        $user = $this->getUser();
        $charter = $user->getCharter();
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter.html.twig', array(
            'title'     => 'My Bookings',
            'current'   => 'boats',
            'id'        => $charter->getId(),
            'response'  => $response->getContent()
        ));
    }
    
    /**
     * Display Charter Payments
     *
     * @return Response
     */
    public function charterPaymentsAction()
    {
        $request    = $this->getRequest();
        $response   = $this->forward('ZizooCharterBundle:Charter:payments', array(), array('redirect_route' => $request->get('_route')));
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
        
        $user = $this->getUser();
        $charter = $user->getCharter();
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter.html.twig', array(
            'title'     => 'My Payments',
            'current'   => 'payments',
            'id'        => $charter->getId(),
            'response'  => $response->getContent()
        ));
    }
    
    
    /**
     * Display Charter Payout Settings
     *
     * @return Response
     */
    public function charterPayoutSettingsAction()
    {
        $request    = $this->getRequest();
        $response   = $this->forward('ZizooCharterBundle:Charter:payoutSettings', array(), array('redirect_route' => $request->get('_route')));
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
        
        $user = $this->getUser();
        $charter = $user->getCharter();
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter.html.twig', array(
            'title'     => 'My Payout Settings',
            'current'   => 'settings',
            'id'        => $charter->getId(),
            'response'  => $response->getContent()
        ));
    }
    
    
    
    /**
     * Display charter add boat 
     *
     * @return Response
     */
    public function charterAction()
    {
        $request    = $this->getRequest();
 
        $params = $request->query->all();
        $params['routes'] = $this->boatRoutes;
        
        $otherController = $request->attributes->get('other_controller');
        
        $response   = $this->forward($otherController, $params);
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
        
        $user = $this->getUser();
 
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter.html.twig', array(
            'title'     => $request->attributes->get('title'),
            'current'   => $request->attributes->get('current'),
            'response'  => $response->getContent()
        ));
    }
    
    
    /**
     * Display Charter Boats
     *
     * @return Response
     */
    public function charterBoatsAction($listing_status)
    {
        $request    = $this->getRequest();
        
        $params = $request->query->all();
        $params['routes'] = $this->boatRoutes;
        
        $response   = $this->forward('ZizooCharterBundle:Charter:boats', array('listing_status' => $listing_status), $params);
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
        
        $user = $this->getUser();
        $charter = $user->getCharter();
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter.html.twig', array(
            'title'     => 'My Boats',
            'current'   => 'boats',
            'id'        => $charter->getId(),
            'response'  => $response->getContent()
        ));
    }
    
    
    /**
     * Display charter add boat 
     *
     * @return Response
     */
    public function charterBoatAction($id=null)
    {
        $request    = $this->getRequest();
 
        $params = $request->query->all();
        $params['routes'] = $this->boatRoutes;
        
        $boatController = $request->attributes->get('boat_controller');
        
        $response   = $this->forward($boatController, array('id' => $id), $params);
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
        
        $user = $this->getUser();
        $charter = $user->getCharter();
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter_boat.html.twig', array(
            'title'     => 'Add Boat',
            'current'   => 'boats',
            'response'  => $response->getContent()
        ));
    }
    
    
    

    
    public function charterTabsAction($current)
    {
        $request            = $this->getRequest();
        $user               = $this->getUser();
        
        $messageManager     = $this->container->get('fos_message.message_manager');
        $numUnreadMessages  = $messageManager->getNbUnreadMessageByParticipant($user);
        
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter_tabs.html.twig', array(
            'current'           => $current,
            'unread_messages'   => $numUnreadMessages
        ));
    }
    
    
    public function userTabsAction($current)
    {
        $request            = $this->getRequest();
        $user               = $this->getUser();
        
        $messageManager     = $this->container->get('fos_message.message_manager');
        $numUnreadMessages  = $messageManager->getNbUnreadMessageByParticipant($user);
        
        return $this->render('ZizooBaseBundle:Dashboard:user_tabs.html.twig', array(
            'current'           => $current,
            'unread_messages'   => $numUnreadMessages
        ));
    }
}