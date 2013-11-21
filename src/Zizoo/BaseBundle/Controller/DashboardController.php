<?php

namespace Zizoo\BaseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    private function isCharterRoute($url)
    {
        $pattern = '/^\/charter\/|^\/app_dev\.php\/charter\//';
        $isCharterRoute = preg_match($pattern, $url);
        return $isCharterRoute;
    }
    
    private function widgetCharterAction($charter, $route)
    {
        $charterService = $this->container->get('zizoo_charter_charter_service');
        $charterCompleteness = $charterService->getCompleteness($charter);

        return $this->render('ZizooBaseBundle:Dashboard:Charter/charter_widget.html.twig', array(
            'charter'       => $charter,
            'completeness'  => $charterCompleteness,
            'route'         => $route,
        ));
    }
    
    private function widgetUserAction($user, $route)
    {
        $facebook       = $this->get('facebook');
        $profileService = $this->container->get('profile_service');
        $profileCompleteness = $profileService->getCompleteness($user->getProfile());

        return $this->render('ZizooBaseBundle:Dashboard:User/user_widget.html.twig', array(
            'user'          => $user,
            'completeness'  => $profileCompleteness,
            'route'         => $route,
            'facebook'      => $facebook
        ));
    }
    
    /**
     * Displays Mini User Profile and Navigation
     * 
     * @return Response
     */
    public function widgetAction($route, $routeParams)
    {
        $user       = $this->getUser();
        $request    = $this->getRequest();
        $url        = $this->generateUrl($route, $routeParams);

        $isCharterRoute = $this->isCharterRoute($url);
        
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
        $activeListings     = $boatRepository->getNumberOfCharterBoats($charter, TRUE, TRUE);
        $incompleteListings = $boatRepository->getNumberOfCharterBoats($charter, FALSE);
        $hiddenListings     = $boatRepository->getNumberOfCharterBoats($charter, FALSE, TRUE);

        $bookingRepository      = $this->getDoctrine()->getRepository('ZizooBookingBundle:Booking');
        $paymentRepository      = $this->getDoctrine()->getRepository('ZizooBookingBundle:Payment');
        $outstandingPayments    = $bookingRepository->getOutstandingBookings($charter);
        $settledPayments        = $paymentRepository->getSettledPayments($charter);

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
            'receivedPayments'      => count($settledPayments),
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
        
        $routes             = $this->container->getParameter('zizoo_base.dashboard_routes.user_routes');
        
        $messageProvider    = $this->container->get('zizoo_message.provider');
        $unreadMessages     = $messageProvider->getNbUnreadMessages();
        
        $latestThreads      = $messageProvider->getThreads(3);
        
        $bookingRepository  = $this->getDoctrine()->getRepository('ZizooBookingBundle:Booking');
        $upcomingBookings   = $bookingRepository->getUpcomingUserBookings($user, 3);
        
        $form = $this->createForm(new SearchBoatType($this->container), new SearchBoat());
        return $this->render('ZizooBaseBundle:Dashboard:User/index.html.twig', array(
            'user'              => $user,
            'reservations'      => $reservationsMade,
            'bookings'          => $bookingsMade,
            'unreadMessages'    => $unreadMessages,
            'latestThreads'     => $latestThreads,
            'upcoming_bookings' => $upcomingBookings,
            'searchForm'        => $form->createView(),
            'routes'            => $routes
        ));
    }
    

    public function userAction()
    {
        $request            = $this->getRequest();
        $params             = $request->query->all();
        $params['routes']   = $this->container->getParameter('zizoo_base.dashboard_routes.user_routes');
        $otherController    = $request->attributes->get('other_controller');
        $title              = $request->attributes->get('title', null);
        $tab                = $request->attributes->get('tab', null);
        $pathParams         = $request->attributes->get('_route_params');
        unset($pathParams['other_controller']);
        unset($pathParams['title']);
        unset($pathParams['tab']);
        //unset($pathParams['routes']);
        
        $response   = $this->forward($otherController, $pathParams, $params);
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
    
        $headers = $response->headers;
        if ($headers->get('x-zizoo-title')){
            $title = $headers->get('x-zizoo-title');
        } 
        if ($title===null) $title = '';
        
        if ($response instanceof JsonResponse) {
            return $response;
        } else {
            return $this->render('ZizooBaseBundle:Dashboard:User/user.html.twig', array(
                'title'     => $title,
                'current'   => $tab,
                'response'  => $response->getContent()
            ));
        }
    }
    
   

    /**
     * Display charter add boat 
     *
     * @return Response
     */
    public function charterAction()
    {
        $request            = $this->getRequest();
        $params             = $request->query->all();
        $params['routes']   = $this->container->getParameter('zizoo_base.dashboard_routes.charter_routes');
        $otherController    = $request->attributes->get('other_controller');
        $title              = $request->attributes->get('title', null);
        $tab                = $request->attributes->get('tab', null);
        $pathParams         = $request->attributes->get('_route_params');
        unset($pathParams['other_controller']);
        unset($pathParams['title']);
        unset($pathParams['tab']);
        
        $response   = $this->forward($otherController, $pathParams, $params);
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
    
        $headers = $response->headers;
        if ($headers->get('x-zizoo-title')){
            $title = $headers->get('x-zizoo-title');
        } 
        if ($title===null) $title = '';
        
        if ($response instanceof JsonResponse) {
            return $response;
        } else {
            return $this->render('ZizooBaseBundle:Dashboard:Charter/charter.html.twig', array(
                'title'     => $title,
                'current'   => $tab,
                'response'  => $response->getContent()
            ));
        }
 
    }
    
    
    /**
     * Display charter add boat 
     *
     * @return Response
     */
    public function charterBoatAction($id=null)
    {
        $request            = $this->getRequest();
        $params             = $request->query->all();
        $params['routes']   = $this->container->getParameter('zizoo_base.dashboard_routes.charter_routes');
        $boatController     = $request->attributes->get('boat_controller');
        $title              = $request->attributes->get('title', null);
        $pathParams         = $request->attributes->get('_route_params');
        unset($pathParams['boat_controller']);
        unset($pathParams['title']);
        
        $response   = $this->forward($boatController, $pathParams, $params);
        
        if ($response->isRedirect()){
            return $this->redirect($response->headers->get('Location'));
        }
        
        $headers = $response->headers;
        if ($headers->get('x-zizoo-title')){
            $title = $headers->get('x-zizoo-title');
        } 
        if ($title===null) $title = '';
        
        if ($response instanceof JsonResponse) {
            return $response;
        } else {
            return $this->render('ZizooBaseBundle:Dashboard:Charter/charter_boat.html.twig', array(
                'title'     => $title,
                'current'   => 'boats',
                'response'  => $response->getContent()
            ), $response);
        }
    }
    
    public function charterTabsAction($current)
    {
        $request            = $this->getRequest();
        $user               = $this->getUser();
        
        $messageManager     = $this->container->get('zizoo_message.message_manager');
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
        
        return $this->render('ZizooBaseBundle:Dashboard:User/user_tabs.html.twig', array(
            'current'           => $current,
            'unread_messages'   => $numUnreadMessages
        ));
    }
}