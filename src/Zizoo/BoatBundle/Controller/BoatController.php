<?php

namespace Zizoo\BoatBundle\Controller;

use Zizoo\BoatBundle\Form\Type\BookBoatType;
use Zizoo\BoatBundle\Form\Model\BookBoat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Boat controller.
 */
class BoatController extends Controller 
{
    /**
     * Show a boat entry
     */
    public function showAction($id) 
    {
        $em = $this->getDoctrine()->getEntityManager();

        $boat = $em->getRepository('ZizooBoatBundle:Boat')->find($id);

        if (!$boat) {
            throw $this->createNotFoundException('Unable to find boat post.');
        }        
        $request = $this->getRequest();
        $request->query->set('url', $this->generateUrl('ZizooBoatBundle_boat_show', array('id' => $id)));
        $request->query->set('ajax_url', $this->generateUrl('ZizooBoatBundle_booking_widget', array('id' => $id, 'request' => $request)));
        return $this->render('ZizooBoatBundle:Boat:show.html.twig', array(
            'boat'      => $boat,
            'request'   => $request
        ));
    }
    
    
    public function bookingWidgetAction($id, Request $request){
        $em = $this->getDoctrine()->getEntityManager();
        $boat = $em->getRepository('ZizooBoatBundle:Boat')->find($id);
        if (!$boat) {
            throw $this->createNotFoundException('Unable to find boat post.');
        }  
        
        $bookBoat = null;
        $form = $this->createForm(new BookBoatType(), new BookBoat($id));
        $form->bindRequest($request);
      
        $bookBoat = $form->getData();
        $bookingAgent = $this->get('booking_agent');
        $availability = $bookingAgent->getAvailability($boat, $bookBoat->getReservationFrom(), $bookBoat->getReservationTo(), $bookBoat->getNumGuests());
        $valid = false;
        $session = $request->getSession();
        if ($form->isValid() && $availability && $bookBoat->getNumGuests()>0){
            $valid = true;
            $session->set('boat', $bookBoat);
        } else {
            $valid = false;
            $session->remove('boat');
        }
        
        $url        = $request->query->get('url', null);
        $ajaxUrl    = $request->query->get('ajax_url', null);
        
        return $this->render('ZizooBoatBundle:Boat:booking_widget.html.twig', array(
            'boat'          => $boat,
            'book_boat'     => $bookBoat,
            'form'          => $form->createView(),
            'availability'  => $availability,
            'valid'         => $valid,
            'url'           => $url,
            'ajax_url'      => $ajaxUrl,
            'book_url'      => $this->generateUrl('zizoo_book')
        ));
    }

}