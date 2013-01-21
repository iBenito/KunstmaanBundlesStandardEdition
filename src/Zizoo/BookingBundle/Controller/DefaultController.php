<?php

namespace Zizoo\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();
        
        $bookingAgent = $this->container->get('booking_agent');
        $boat = $em->getRepository('ZizooBoatBundle:Boat')->findById('469');
        $user =  $em->getRepository('ZizooUserBundle:User')->findById('562');
        
        $availabilities = $boat[0]->getAvailability();
        if ($availabilities && $availabilities->offsetExists(0)){
            $availability = $availabilities->first();
            $from   = clone $availability->getAvailableFrom();
            $to     = clone $availability->getAvailableUntil();
            
            $from->modify('+1 week');
            $to->modify('-1 week');
            
            $reservation = $bookingAgent->makeReservation($boat[0], $user[0], $from, $to, 9.99);
            var_dump($reservation);
            exit();
        }
        
        
        return $this->render('ZizooBookingBundle:Default:index.html.twig', array('name' => $name));
    }
}
