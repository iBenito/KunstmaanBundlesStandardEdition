<?php

namespace Zizoo\ReservationBundle\Controller;

use Zizoo\ReservationBundle\Form\Type\AcceptReservationType;
use Zizoo\ReservationBundle\Form\Model\AcceptReservation;
use Zizoo\ReservationBundle\Form\Type\DenyReservationType;
use Zizoo\ReservationBundle\Form\Model\DenyReservation;
use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\BoatBundle\Form\Model\BookBoat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ReservationController extends Controller
{

    /**
     * View reservation.
     * @param type $id
     */
    public function viewReservationAction($id)
    {
        $request    = $this->getRequest();
        $ajax       = $request->isXmlHttpRequest();
        $user       = $this->getUser();
        
        $em                 = $this->getDoctrine()->getManager();
        $reservation        = $em->getRepository('ZizooReservationBundle:Reservation')->findOneById($id);
        if (!$reservation || $reservation->getBoat()->getCharter()->getAdminUser()!=$user){
            return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard'));
        }
                
        $reservationStatus  = $reservation->getStatus();
        $showControls       = $reservationStatus==Reservation::STATUS_REQUESTED;
        
        return $this->render('ZizooReservationBundle:Reservation:view_reservation.html.twig', array(
            'reservation'   => $reservation,
            'show_controls' => $showControls,
            'ajax'          => $ajax,
        ));
    }
    
    /**
     * Accept reservation request. User must confirm denial of other overlapping reservation requests.
     * @param type $id
     */
    public function acceptReservationRequestAction($id)
    {
        $request    = $this->getRequest();
        $ajax       = $request->isXmlHttpRequest();
        $user       = $this->getUser();
        
        $em                 = $this->getDoctrine()->getManager();
        $reservation        = $em->getRepository('ZizooReservationBundle:Reservation')->findOneById($id);
        if (!$reservation || $reservation->getBoat()->getCharter()->getAdminUser()!=$user){
            return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard'));
        }
        
        $reservationAgent   = $this->get('zizoo_reservation_reservation_agent');
        $bookingAgent       = $this->get('zizoo_booking_booking_agent');
        $trans              = $this->get('translator');
        
        $thread             = $em->getRepository('ZizooMessageBundle:Thread')->findOneByReservation($reservation);
        
        $overlappingReservationRequests = $em->getRepository('ZizooReservationBundle:Reservation')
                                                ->getReservations($user, $reservation->getBoat(), 
                                                                    $reservation->getCheckIn(), $reservation->getCheckOut(),
                                                                    array(Reservation::STATUS_REQUESTED), $reservation);
        
        $form = $this->createForm(new AcceptReservationType(), new AcceptReservation($overlappingReservationRequests));
        
        if ($request->isMethod('post')){
            if ($request->request->get('accept', null)){
                $form->bind($request);
                if ($form->isValid()){
                    $acceptReservation = $form->getData();
                    
                    try {
                        // Reject any overlapping reservation requests
                        foreach ($overlappingReservationRequests as $overlappingReservationRequest){
                            $reservationAgent->denyReservation($overlappingReservationRequest, false);
                            $bookingAgent->void($overlappingReservationRequest->getBooking(), false);
                            $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_reservation.request_denied_success'));
                        }

                    } catch (\Exception $e){
                        $this->get('session')->getFlashBag()->add('error', $trans->trans('zizoo_reservation.request_denied_error'));
                        return $this->redirect($this->generateUrl('ZizooReservationBundle_view', array('id' => $id)));
                    }
                    
                    try {
                        // Accept reservation request
                        $reservationAgent->acceptReservation($reservation, false);
                        $bookingAgent->submitForSettlement($reservation->getBooking(), false);

                        $composer           = $this->container->get('zizoo_message.composer');
                        $sender             = $this->container->get('fos_message.sender');
                        $messageTypeRepo    = $this->container->get('doctrine.orm.entity_manager')->getRepository('ZizooMessageBundle:MessageType');
                        
                        if ($thread){
                            // Send accept message
                            $thread = $composer->reply($thread)
                                                ->setSender($user)
                                                ->setBody($acceptReservation->getAcceptMessage());

                            $message =  $thread->getMessage()
                                                ->setMessageType($messageTypeRepo->findOneById('accepted'));

                            $sender->send($message);
                        }
                        
                        // Send deny message for each denied reservation request
                        foreach ($overlappingReservationRequests as $overlappingReservationRequest){
                            $thread = $em->getRepository('ZizooMessageBundle:Thread')->findOneByReservation($overlappingReservationRequest);
                            if ($thread){
                                $thread = $composer->reply($thread)
                                                    ->setSender($user)
                                                    ->setBody($acceptReservation->getDenyReservation()->getDenyMessage());

                                $message =  $thread->getMessage()
                                                    ->setMessageType($messageTypeRepo->findOneById('declined'));

                                $sender->send($message);
                            }
                        }
                        
                        $em->flush();
                        $this->get('session')->getFlashBag()->add('error', $trans->trans('zizoo_reservation.request_accepted_success'));
                        return $this->redirect($this->generateUrl('ZizooReservationBundle_view', array('id' => $id)));
                        
                    } catch (\Exception $e){
                        $this->get('session')->getFlashBag()->add('error', $trans->trans('zizoo_reservation.request_accepted_error'));
                        return $this->redirect($this->generateUrl('ZizooReservationBundle_view', array('id' => $id)));
                    }
                } else {
                    //return $this->redirect($this->generateUrl('ZizooReservationBundle_view', array('id' => $id)));
                }
            }
        }
        
        return $this->render('ZizooReservationBundle:Reservation:accept_reservation.html.twig', array(
            'reservation'                       => $reservation,
            'form'                              => $form->createView(),
            'overlap_requested_reservations'    => $overlappingReservationRequests,
            'ajax'                              => $ajax,
        ));
        
    }
    
    public function denyReservationRequestAction($id)
    {
        $request    = $this->getRequest();
        $ajax       = $request->isXmlHttpRequest();
        
        $user       = $this->getUser();
        
        $em                 = $this->getDoctrine()->getManager();
        $reservation        = $em->getRepository('ZizooReservationBundle:Reservation')->findOneById($id);
        if (!$reservation || $reservation->getBoat()->getCharter()->getAdminUser()!=$user){
            return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard'));
        }
        
        $reservationAgent   = $this->get('zizoo_reservation_reservation_agent');
        $bookingAgent       = $this->get('zizoo_booking_booking_agent');
        $trans              = $this->get('translator');
        
        $thread             = $em->getRepository('ZizooMessageBundle:Thread')->findOneByReservation($reservation);
        
        $form = $this->createForm(new DenyReservationType(), new DenyReservation());
        
        if ($request->isMethod('post')){
            $form->bind($request);
            if ($form->isValid()){
                if ($request->request->get('deny', null)){
                    try {
                        $reservationAgent->denyReservation($reservation, false);
                        $bookingAgent->void($reservation->getBooking(), true);

                        if ($thread){
                            $composer       = $this->container->get('zizoo_message.composer');
                            $sender         = $this->container->get('fos_message.sender');
                            $messageTypeRepo = $this->container->get('doctrine.orm.entity_manager')->getRepository('ZizooMessageBundle:MessageType');

                            $denyReservation = $form->getData();

                            $thread = $composer->reply($thread)
                                                ->setSender($user)
                                                ->setBody($denyReservation->getDenyMessage());

                            $message =  $thread->getMessage()
                                                ->setMessageType($messageTypeRepo->findOneById('denied'));

                            $sender->send($message);
                        }

                        $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_reservation.request_denied_success'));
                        return $this->redirect($this->generateUrl('ZizooReservationBundle_view', array('id' => $id)));
                    } catch (\Exception $e){
                        $this->get('session')->getFlashBag()->add('error', $trans->trans('zizoo_reservation.request_denied_error'));
                        return $this->redirect($this->generateUrl('ZizooReservationBundle_view', array('id' => $id)));
                    }
                } else {
                    return $this->redirect($this->generateUrl('ZizooReservationBundle_view', array('id' => $id)));
                }
            }
        }
        
        return $this->render('ZizooReservationBundle:Reservation:deny_reservation.html.twig', array(
            'reservation'   => $reservation,
            'form'          => $form->createView(),
            'ajax'          => $ajax,
        ));
        
    }
}
