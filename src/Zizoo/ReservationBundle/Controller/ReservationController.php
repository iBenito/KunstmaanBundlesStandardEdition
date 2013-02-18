<?php

namespace Zizoo\ReservationBundle\Controller;

use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\BoatBundle\Form\Model\BookBoat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ReservationController extends Controller
{
    public function testAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $boat = $em->getRepository('ZizooBoatBundle:Boat')->findOneById($id);
        
        $reservationAgent = $this->container->get('zizoo_reservation_reservation_agent');
        
        $from   = new \DateTime();
        $to     = new \DateTime();
        $from->modify('+2 years');
        $to->modify('+3 years');

        $bookBoat = new BookBoat($boat->getID());
        $bookBoat->setNumGuests(50);
        $bookBoat->setReservationFrom($from);
        $bookBoat->setReservationTo($to);
        
        $reservation = $reservationAgent->makeReservation($boat, $bookBoat, true);
        
        var_dump($reservation);
    }
}
