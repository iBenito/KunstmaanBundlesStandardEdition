<?php

namespace Zizoo\BaseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\DBAL\DBALException;

use Zizoo\ReservationBundle\Entity\Reservation;


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
    public function widgetAction($route, $showUser=false)
    {
        $user = $this->getUser();

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
        $reservationRequests = $this->getDoctrine()->getRepository('ZizooReservationBundle:Reservation')->getReservationRequests($charter);
        
        return $this->render('ZizooBaseBundle:Dashboard:Charter/index.html.twig', array(
            'reservationRequests' => $reservationRequests
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

        return $this->render('ZizooBaseBundle:Dashboard:index.html.twig', array(
            'reservations' => $reservationsMade,
            'bookings' => $bookingsMade
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
        return $this->render('ZizooBaseBundle:Dashboard:profile.html.twig', array(
            'username' => $this->getUser()->getUsername()
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
        $user = $this->getUser();

        $bookings = $user->getBookings();

        return $this->render('ZizooBaseBundle:Dashboard:trips.html.twig', array(
            'bookings' => $bookings
        ));
    }

}