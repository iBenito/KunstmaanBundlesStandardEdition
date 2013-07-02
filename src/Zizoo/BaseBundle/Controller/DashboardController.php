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

    private function widgetCharterAction($charter, $route)
    {
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter_widget.html.twig', array(
            'charter'   => $charter,
            'route'     => $route,
        ));
    }
    
    private function widgetUserAction($user, $route, $showUser)
    {
        $profileService = $this->get('profile_service');
        $profileCompleteness = $profileService->getCompleteness($user->getProfile());

        return $this->render('ZizooBaseBundle:Dashboard:user_widget.html.twig', array(
            'user'      => $user,
            'route'     => $route,
            'show_user' => $showUser,
            'profile_completeness' => $profileCompleteness
        ));
    }
    
    /**
     * Displays Mini User Profile and Navigation
     * 
     * @return Response
     */
    public function widgetAction($route)
    {
        $request    = $this->getRequest();
        $user       = $this->getUser();
        
        $showUser = $request->getSession()->get('show_user');
        if ($user->getCharter() && !$showUser){
            return $this->widgetCharterAction($user->getCharter(), $route);
        } else {
            return $this->widgetUserAction($user, $route, $showUser);
        }
    }
    
    
    /**
     * Display Charter Dashboard
     * 
     * @return Response
     */
    private function indexCharterAction($charter)
    {
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
    private function indexUserAction($user)
    {
        $reservationsMade = $user->getReservations();
        $bookingsMade = $user->getBookings();

        $form = $this->createForm(new SearchBoatType($this->container), new SearchBoat());
        return $this->render('ZizooBaseBundle:Dashboard:index.html.twig', array(
            'reservations' => $reservationsMade,
            'bookings' => $bookingsMade,
            'searchForm' => $form->createView()
        ));
    }
    
    /**
     * Display User or Charter Dashboard
     * 
     * @return Response
     */
    public function indexAction()
    {
        $request    = $this->getRequest();
        $showUser   = $request->query->get('show_user', false);
        $user       = $this->getUser();
        
        $request->getSession()->set('show_user', $showUser);
        
        if ($user->getCharter() && !$showUser){
            return $this->indexCharterAction($user->getCharter());
        } else {
            return $this->indexUserAction($user);
        }
        
    }

    /**
     * Display User Inbox
     * 
     * @return Response
     */
    public function inboxAction()
    {
        return $this->render('ZizooBaseBundle:Dashboard:inbox.html.twig', array(

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
        $response   = $this->forward('ZizooProfileBundle:Profile:edit');
        
        if ($response->isRedirect()){
            return $this->redirect($this->generateUrl($request->get('_route')));
        }
        
        return $this->render('ZizooBaseBundle:Dashboard:profile.html.twig', array(
            'username'  => $this->getUser()->getUsername(),
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
        $response   = $this->forward('ZizooCharterBundle:Charter:profile');
        
        if ($response->isRedirect()){
            return $this->redirect($this->generateUrl($request->get('_route')));
        }
        
        $user = $this->getUser();

        $bookings = $user->getBookings();

        return $this->render('ZizooBaseBundle:Dashboard:trips.html.twig', array(
            'bookings' => $bookings
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
        $response   = $this->forward('ZizooCharterBundle:Charter:profile');
        
        if ($response->isRedirect()){
            return $this->redirect($this->generateUrl($request->get('_route')));
        }
        
        $user = $this->getUser();
        $charter = $user->getCharter();
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter_profile.html.twig', array(
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
        $response   = $this->forward('ZizooMessageBundle:Message:inbox');
        
        if ($response->isRedirect()){
            return $this->redirect($this->generateUrl($request->get('_route')));
        }
        
        $user = $this->getUser();
        $charter = $user->getCharter();
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter_inbox.html.twig', array(
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
        $response   = $this->forward('ZizooMessageBundle:Message:sent');
        
        if ($response->isRedirect()){
            return $this->redirect($this->generateUrl($request->get('_route')));
        }
        
        $user = $this->getUser();
        $charter = $user->getCharter();
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter_sent.html.twig', array(
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
        $response   = $this->forward('ZizooCharterBundle:Charter:bookings');
        
        if ($response->isRedirect()){
            return $this->redirect($this->generateUrl($request->get('_route')));
        }
        
        $user = $this->getUser();
        $charter = $user->getCharter();
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter_bookings.html.twig', array(
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
        $response   = $this->forward('ZizooCharterBundle:Charter:payments');
        
        if ($response->isRedirect()){
            return $this->redirect($this->generateUrl($request->get('_route')));
        }
        
        $user = $this->getUser();
        $charter = $user->getCharter();
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter_payments.html.twig', array(
            'id'        => $charter->getId(),
            'response'  => $response->getContent()
        ));
    }
    
    /**
     * Display Charter Boats
     *
     * @return Response
     */
    public function charterBoatsAction()
    {
        $request    = $this->getRequest();
        $response   = $this->forward('ZizooCharterBundle:Charter:boats');
        
        if ($response->isRedirect()){
            return $this->redirect($this->generateUrl($request->get('_route')));
        }
        
        $user = $this->getUser();
        $charter = $user->getCharter();
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter_boats.html.twig', array(
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
        $response   = $this->forward('ZizooCharterBundle:Charter:payoutSettings');
        
        if ($response->isRedirect()){
            return $this->redirect($this->generateUrl($request->get('_route')));
        }
        
        $user = $this->getUser();
        $charter = $user->getCharter();
        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter_payout_settings.html.twig', array(
            'id'        => $charter->getId(),
            'response'  => $response->getContent()
        ));
    }
    
}