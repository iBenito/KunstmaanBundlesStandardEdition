<?php

namespace Zizoo\ReservationBundle\Controller;

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
        
        $em                 = $this->getDoctrine()->getEntityManager();
        $reservation        = $em->getRepository('ZizooReservationBundle:Reservation')->findOneById($id);
        if (!$reservation || $reservation->getBoat()->getUser()!=$user){
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
        
        $em                 = $this->getDoctrine()->getEntityManager();
        $reservation        = $em->getRepository('ZizooReservationBundle:Reservation')->findOneById($id);
        if (!$reservation || $reservation->getBoat()->getUser()!=$user){
            return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard'));
        }
        
        $reservationAgent   = $this->get('zizoo_reservation_reservation_agent');
        $bookingAgent       = $this->get('zizoo_booking_booking_agent');
        $trans              = $this->get('translator');
        
        $thread             = $em->getRepository('ZizooMessageBundle:Thread')->findOneByReservation($reservation);
        
        $acceptForm         = $this->container->get('zizoo_message.reply_form.factory')->create($thread);
        $denyForm           = $this->container->get('zizoo_message.reply_form.factory')->create($thread);
        
        $overlappingReservationRequests = $em->getRepository('ZizooReservationBundle:Reservation')
                                                ->getReservations($user, $reservation->getBoat(), 
                                                                    $reservation->getCheckIn(), $reservation->getCheckOut(),
                                                                    array(Reservation::STATUS_REQUESTED), $reservation);
        if ($request->isMethod('post')){
            if ($request->request->get('accept', null)){
                
                if (count($overlappingReservationRequests)>0){
                    $denyForm->bindRequest($request);
                    if ($denyForm->isValid()){
                        
                        try {
                            foreach ($overlappingReservationRequests as $overlappingReservationRequest){
                                $reservationAgent->denyReservation($overlappingReservationRequest, false);
                                $bookingAgent->void($overlappingReservationRequest->getBooking(), false);
                            }
 
                            $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_reservation.request_denied_success'));
                            
                        } catch (\Exception $e){
                            $this->get('session')->getFlashBag()->add('error', $trans->trans('zizoo_reservation.request_denied_error'));
                            return $this->redirect($this->generateUrl('ZizooReservationBundle_view', array('id' => $id)));
                        }
                        
                    }
                    
                }
                
                $acceptForm->bindRequest($request);
                if ($acceptForm->isValid()){

                    try {
                        $reservationAgent->acceptReservation($reservation, false);
                        $bookingAgent->submitForSettlement($reservation->getBooking(), false);

                        $composer           = $this->container->get('zizoo_message.composer');
                        $sender             = $this->container->get('fos_message.sender');
                        $messageTypeRepo    = $this->container->get('doctrine.orm.entity_manager')->getRepository('ZizooMessageBundle:MessageType');
                        
                        if ($thread){
                            $replyMessage = $acceptForm->getData();

                            $thread = $composer->reply($thread)
                                                ->setSender($user)
                                                ->setBody($replyMessage->getBody());

                            $message =  $thread->getMessage()
                                                ->setMessageType($messageTypeRepo->findOneById('accepted'));

                            $sender->send($message);
                        }

                        if (count($overlappingReservationRequests)>0 && $denyForm->isValid()){
                            foreach ($overlappingReservationRequests as $overlappingReservationRequest){
                                
                                $thread = $em->getRepository('ZizooMessageBundle:Thread')->findOneByReservation($overlappingReservationRequest);
                                if ($thread){
                                    
                                    $replyMessage = $denyForm->getData();

                                    $thread = $composer->reply($thread)
                                                        ->setSender($user)
                                                        ->setBody($replyMessage->getBody());

                                    $message =  $thread->getMessage()
                                                        ->setMessageType($messageTypeRepo->findOneById('declined'));

                                    $sender->send($message);
                                }
                            }
                        }
                        
                        $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_reservation.request_accepted_success'));
                        return $this->redirect($this->generateUrl('ZizooReservationBundle_view', array('id' => $id)));
                    } catch (\Exception $e){
                        $this->get('session')->getFlashBag()->add('error', $trans->trans('zizoo_reservation.request_accepted_error'));
                        return $this->redirect($this->generateUrl('ZizooReservationBundle_view', array('id' => $id)));
                    }
                } else {
                    return $this->redirect($this->generateUrl('ZizooReservationBundle_view', array('id' => $id)));
                }
            }
        }
        
        return $this->render('ZizooReservationBundle:Reservation:accept_reservation.html.twig', array(
            'reservation'                       => $reservation,
            'accept_form'                       => $acceptForm->createView(),
            'deny_form'                         => $denyForm->createView(),
            'overlap_requested_reservations'    => $overlappingReservationRequests,
            'ajax'                              => $ajax,
        ));
        
    }
    
    public function denyReservationRequestAction($id)
    {
        $request    = $this->getRequest();
        $ajax       = $request->isXmlHttpRequest();
        
        $user       = $this->getUser();
        
        $em                 = $this->getDoctrine()->getEntityManager();
        $reservation        = $em->getRepository('ZizooReservationBundle:Reservation')->findOneById($id);
        if (!$reservation || $reservation->getBoat()->getUser()!=$user){
            return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard'));
        }
        
        $reservationAgent   = $this->get('zizoo_reservation_reservation_agent');
        $bookingAgent       = $this->get('zizoo_booking_booking_agent');
        $trans              = $this->get('translator');
        
        $thread             = $em->getRepository('ZizooMessageBundle:Thread')->findOneByReservation($reservation);
        
        $form               = $this->container->get('zizoo_message.reply_form.factory')->create($thread);
        
        if ($request->isMethod('post')){
            $form->bindRequest($request);
            if ($form->isValid()){
                if ($request->request->get('deny', null)){
                    try {
                        $reservationAgent->denyReservation($reservation, false);
                        $bookingAgent->void($reservation->getBooking(), true);

                        if ($thread){
                            $composer       = $this->container->get('zizoo_message.composer');
                            $sender         = $this->container->get('fos_message.sender');
                            $messageTypeRepo = $this->container->get('doctrine.orm.entity_manager')->getRepository('ZizooMessageBundle:MessageType');

                            $replyMessage = $form->getData();

                            $thread = $composer->reply($thread)
                                                ->setSender($user)
                                                ->setBody($replyMessage->getBody());

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
