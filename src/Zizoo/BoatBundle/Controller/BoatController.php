<?php

namespace Zizoo\BoatBundle\Controller;

use Zizoo\BoatBundle\Form\Type\BoatDetailsType;
use Zizoo\BoatBundle\Form\Type\BookBoatType;
use Zizoo\BoatBundle\Form\Type\ConfirmBoatPriceType;
use Zizoo\BoatBundle\Form\Model\BookBoat;
use Zizoo\BoatBundle\Form\Model\ConfirmBoatPrice;
use Zizoo\ReservationBundle\Entity\Reservation;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

use Zizoo\BoatBundle\Entity\Boat;

use Zizoo\BoatBundle\Entity\Image;
use Zizoo\BoatBundle\Form\Type\BoatImageType;
use Zizoo\BoatBundle\Form\Type\BoatType;

/**
 * Boat controller.
 * 
 * @author Alex Fuckert <alexf83@gmail.com>
 * @author Benito Gonzalez <vbenitogo@gmail.com>
 */
class BoatController extends Controller 
{
    /**
     * Show a boat entry
     */
    public function showAction($id) 
    {
        $em = $this->getDoctrine()->getManager();

        $boat = $em->getRepository('ZizooBoatBundle:Boat')->find($id);

        if (!$boat || !$boat->getActive() || $boat->getDeleted()){
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }
        
        $map = $this->get('ivory_google_map.map');
        $map->setAsync(true);
        $map->setAutoZoom(false);
        $boatAddress = $boat->getAddress();
        if ($boatAddress->getLat() && $boatAddress->getLng()){
            $map->setCenter($boatAddress->getLat(), $boatAddress->getLng(), true);
        }
        $map->setMapOption('zoom', 4);
        $map->setMapOption('disableDefaultUI', true);
        $map->setMapOption('zoomControl', true);
        $map->setStylesheetOptions(array(
            'width' => '100%',
            'height' => '300px'
        ));
        
        if ($boatAddress->getLat() && $boatAddress->getLng()){
            $marker = $this->get('ivory_google_map.marker');
            $marker->setPosition($boatAddress->getLat(), $boatAddress->getLng(), true);
            $marker->setOption('title', $boat->getName());
            $marker->setOption('clickable', true);
            $marker->setIcon('http://www.incrediblue.com/assets/map-pin.png');
            
            /** info window
            * 

           // Requests the ivory google map info window service
           $infoWindow = $this->get('ivory_google_map.info_window');
           // Add your info window to the marker
           $marker->setInfoWindow($infoWindow);
           */

           /** Event
            * 

           // Requests the ivory google map event service
           $event = $this->get('ivory_google_map.event');

           $instance = $marker->getJavascriptVariable();
           // Configure your event
           $handle = 'function(){alert("The event has been triggered");}';
           $event->setInstance($instance);
           $event->setEventName('click');
           $event->setHandle($handle);

           // It can only be used with a DOM event
           // By default, the capture flag is false
           $event->setCapture(true);

            // Add a DOM event
           $map->getEventManager()->addDomEvent($event);
           */
           $map->addMarker($marker);
        }
        
        $equipment = $em->getRepository('ZizooBoatBundle:Equipment')->findAll();
        $amenities = $em->getRepository('ZizooBoatBundle:Amenities')->findAll();

        $reservations   = $boat->getReservation();
        $prices         = $boat->getPrice();
        
        $request = $this->getRequest();       
        $request->query->set('url', $this->generateUrl('ZizooBoatBundle_Boat_Show', array('id' => $id)));
        $request->query->set('ajax_url', $this->generateUrl('ZizooBoatBundle_booking_widget', array('id' => $id, 'request' => $request)));
        return $this->render('ZizooBoatBundle:Boat:show.html.twig', array(
            'boat'              => $boat,
            'map'               => $map,
            'reservations'      => $reservations,
            'prices'            => $prices,
            'equipment'         => $equipment,
            'amenities'   => $amenities,
            'request'           => $request
        ));
    }
    
    private function allParametersSet($request)
    {
        $boatBookArr = $request->query->get('zizoo_boat_book', null);
        if (!$boatBookArr) return false;
        if (!array_key_exists('reservation_range', $boatBookArr)) return false;
        $reservationRange = $boatBookArr['reservation_range'];
        if (!array_key_exists('reservation_from', $reservationRange)) return false;
        $from = $reservationRange['reservation_from'];
        if ($from=='') return false;
        try {
            \DateTime::createFromFormat('d/m/Y', $from);
        } catch (\Exception $e){
            return false;
        }
        if (!array_key_exists('reservation_to', $reservationRange)) return false;
        $to = $reservationRange['reservation_to'];
        if ($to=='') return false;
        try {
            \DateTime::createFromFormat('d/m/Y', $to);
        } catch (\Exception $e){
            return false;
        }
        return true;
    }
    
    public function bookingWidgetAction($id, Request $request, $reservations=null, $prices=null){
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $boat = $em->getRepository('ZizooBoatBundle:Boat')->find($id);
        if (!$boat) {
            throw $this->createNotFoundException('Unable to find boat post.');
        }  
        
        $valid = false;
        $totals = null;
        
        $crew = !$boat->getCrewOptional();
        $boatBookArr = $request->query->get('zizoo_boat_book', null);
        if ($boatBookArr && array_key_exists('crew', $boatBookArr)) $crew = $boatBookArr['crew']=='true';
        $bookBoat = new BookBoat($id, $crew);
                
        $form = $this->createForm('zizoo_boat_book', $bookBoat, array());
        if ($request->isMethod('post') || $this->allParametersSet($request)){
            $form->bind($request);
            $bookBoat = $form->getData();

            $reservationAgent = $this->container->get('zizoo_reservation_reservation_agent');
            try {
                $reservationRange = $bookBoat->getReservationRange();
                $from   = $reservationRange?$reservationRange->getReservationFrom():null;
                $until  = $reservationRange?$reservationRange->getReservationTo():null;
                $totals = $reservationAgent->getTotalPrice($boat, $from, $until, $bookBoat->getCrew(), true);
                $bookBoat->setSubtotal($totals['subtotal']);
                $bookBoat->setCrewPrice($totals['crew_price']);
                $bookBoat->setTotal($totals['total']);
            } catch (\Zizoo\ReservationBundle\Exception\InvalidReservationException $e){
                $totals = null;
            }

            if ($form->isValid() && $bookBoat->getNumGuests()>0){
                $valid = true;
                $session->set('boat', $bookBoat);
            } else {
                $valid = false;
                $session->remove('boat');
            }
        }
        
        if (!$reservations) $reservations = $boat->getReservation();
        if (!$prices) $prices = $boat->getPrice();
        
        $url            = $request->query->get('url', null);
        $ajaxUrl        = $request->query->get('ajax_url', null);
        if (!$url)      $url = $request->request->get('url', null);
        if (!$ajaxUrl)  $ajaxUrl = $request->request->get('ajax_url');
        
        return $this->render('ZizooBoatBundle:Boat:booking_widget.html.twig', array(
            'boat'                  => $boat,
            'book_boat'             => $bookBoat,
            'subtotal'              => $totals?$totals['subtotal']:null,
            'crew_price'            => $totals?$totals['crew_price']:null,
            'total'                 => $totals?$totals['total']:null,
            'form'                  => $form->createView(),
            'valid'                 => $valid,
            'reservations'          => $reservations,
            'prices'                => $prices,
            'url'                   => $url,
            'ajax_url'              => $ajaxUrl,
            'book_url'              => $this->generateUrl('ZizooBookingBundle_book')
        ));
    }
    
    /**
     * Create input form for Boat, re-used by New and Edit Boat Actions
     */
    public function boatFormWidgetAction(Boat $boat, $formAction)
    {
        $form = $this->createForm(new BoatType(), $boat);

        return $this->render('ZizooBoatBundle:Boat:boat_form_widget.html.twig', array(
            'boat' => $boat,
            'form' => $form->createView(),
            'formAction' => $formAction,
        ));
    }
    
    /**
     * Displays a form to create a new Boat entity.
     *
     */
    public function newAction()
    {
        $boat = new Boat();

        $session = $this->get('session');
        $session->set('step', 'one');

        return $this->render('ZizooBoatBundle:Boat:new.html.twig', array(
            'boat' => $boat,
            'formAction' => 'ZizooBoatBundle_Boat_Create'
        ));
    }

    /**
     * Creates a new Boat entity.
     *
     */
    public function createAction(Request $request)
    {
        $user       = $this->getUser();
        $charter    = $user->getCharter();

        $boat = new Boat();
        $form = $this->createForm(new BoatType(), $boat);
        $form->bind($request);

        if ($form->isValid()) {
            $boat->setCharter($charter);
            
            /* Boat creation is done by Boat Service class */
            $boatService = $this->get('boat_service');
            $boatCreated = $boatService->createBoat($boat, $boat->getAddress(), $boat->getBoatType(), $charter, null, true);
            
            return $this->redirect($this->generateUrl('ZizooBoatBundle_Boat_EditDetails', array('id' => $boatCreated->getId())));
        }

        return $this->render('ZizooBoatBundle:Boat:new.html.twig', array(
            'boat' => $boat,
            'form' => $form->createView(),
            'formAction' => 'ZizooBoatBundle_Boat_Create'
        ));
    }

    /**
     * Displays a form to edit an existing Boat entity.
     *
     */
    public function editAction($id)
    {
        $em         = $this->getDoctrine()->getManager();
        $boat       = $em->getRepository('ZizooBoatBundle:Boat')->find($id);

        $session = $this->get('session');
        $session->set('step', NULL);

        return $this->render('ZizooBoatBundle:Boat:edit.html.twig', array(
            'boat'          => $boat,
            'formAction'    => 'ZizooBoatBundle_Boat_Update'
        ));
    }

    /**
     * Displays a form to edit an existing Boat entity.
     *
     */
    public function editDetailsAction($id)
    {
        $em     = $this->getDoctrine()->getManager();
        $boat   = $em->getRepository('ZizooBoatBundle:Boat')->find($id);

        $detailsForm = $this->createForm(new BoatDetailsType(), $boat);

        return $this->render('ZizooBoatBundle:Boat:editDetails.html.twig', array(
            'boat'          => $boat,
            'form'          => $detailsForm->createView(),
            'formAction'    => 'ZizooBoatBundle_Boat_Update',
        ));
    }

    /**
     * Displays a form to edit an existing Boat entity.
     *
     */
    public function editPhotosAction(Request $request, $id)
    {
        $boat = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat')->find($id);
        if (!$boat){
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }

        $originalPhotos = array();
        // Create an array of the current Boat Photos objects in the database
        foreach ($boat->getImage() as $photo) {
            $originalPhotos[] = $photo;
        }

        $imagesForm = $this->createForm($this->get('zizoo_boat.boat_image_type'), $boat);

        if ($request->isMethod('post')){
            $imagesForm->bind($request);

            if ($imagesForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $boat = $imagesForm->getData();

                //setting the updated field manually for file upload DO NOT REMOVE
                $boat->setUpdated(new \DateTime());

                $images = $boat->getImage();

                $now = new \DateTime();
                // filter $originalAvatars to contain avatars no longer present
                foreach ($images as $image) {
                    foreach ($originalPhotos as $key => $toDel) {
                        if ($toDel->getId() === $image->getId()) {
                            unset($originalPhotos[$key]);
                        }
                    }
                    $image->setUpdated($now);
                    $em->persist($image);
                }

                // remove the relationship between the avatar and the profile
                foreach ($originalPhotos as $photo) {
                    // remove the ProvileAvatar from the Profile
                    $boat->removeImage($photo);

                    // remove the avatar completely
                    $em->remove($photo);
                }

                $em->persist($boat);

                $em->flush();
                return $this->redirect($this->generateUrl('ZizooBoatBundle_Boat_EditPhotos', array('id' => $id)));
            }
        }

        return $this->render('ZizooBoatBundle:Boat:photos.html.twig', array(
            'boat'  => $boat,
            'imagesForm'  => $imagesForm->createView()
        ));

    }

    /**
     * Displays a form to edit an existing Boat entity.
     *
     */
    public function editPriceAction($id)
    {
        $boat   = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat')->find($id);
        if (!$boat){
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ZizooBoatBundle:Boat:edit.html.twig', array(
            'boat'          => $boat,
            'delete_form'   => $deleteForm->createView(),
            'formAction'    => 'ZizooBoatBundle_update',
            'formRedirect'  => 'ZizooBoatBundle_Boat_Edit'
        ));
    }

    /**
     * Edits an existing Boat entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em     = $this->getDoctrine()->getManager();
        $user   = $this->getUser();
        
        $boat   = $em->getRepository('ZizooBoatBundle:Boat')->find($id);

        //if (!$boat || $boat->getCharter()->getAdminUser()!=$user) {
        if (!$boat || !$charter->getUsers()->contains($user)){
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new BoatType(), $boat);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            //$boat->getAddress()->fetchGeo();
            //$boat = $editForm->getData();
            $em->persist($boat);
            $em->flush();

            $redirect = $request->query->get('formRedirect');
            return $this->redirect($this->generateUrl($redirect, array('id' => $id)));
        }

        return $this->render('ZizooBoatBundle:Boat:edit.html.twig', array(
            'boat'      => $boat,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Boat entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em                 = $this->getDoctrine()->getManager();
        $boat               = $em->getRepository('ZizooBoatBundle:Boat')->find($id);
        $user               = $this->getUser();
        $charter            = $user->getCharter();
        $boatService        = $this->get('boat_service');
        $reservationAgent   = $this->get('zizoo_reservation_reservation_agent');
        $bookingAgent       = $this->get('zizoo_booking_booking_agent');
        $trans              = $this->get('translator');
        
        //if (!$boat || $charter->getAdminUser()!=$user) {
        if (!$boat || !$charter->getUsers()->contains($user)){
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }
        
        $form = $this->createDeleteForm($id);
        
        $reservationRequests    = $em->getRepository('ZizooReservationBundle:Reservation')
                                        ->getReservations(  $charter, null, null, 
                                                            null, null,
                                                            array(Reservation::STATUS_REQUESTED), null);
        
        $now = new \DateTime();
        
        $futureReservations     = $em->getRepository('ZizooReservationBundle:Reservation')
                                        ->getReservations(  $charter, null, null, 
                                                            null, $now,
                                                            array(Reservation::STATUS_ACCEPTED, Reservation::STATUS_HOLD), null);
        
        if ($request->isMethod('post')){
            $form->bind($request);

            if ($form->isValid()) {

                try {
                    
                    $delete = $boat->getDeleted()==null;
                    if ($delete){
                        // Reject any outstanding reservation requests
                        foreach ($reservationRequests as $reservationRequest){
                            $reservationAgent->denyReservation($reservationRequest, false);
                            $bookingAgent->void($reservationRequest->getBooking(), false);
                            $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_reservation.request_denied_success'));
                        }
                    }
                    
                    $boatService->deleteBoat($boat, $delete);
                    if ($delete){
                        $this->container->get('session')->getFlashBag()->add('notice', $boat->getName() . ' was deleted successfully');
                    } else {
                        $this->container->get('session')->getFlashBag()->add('notice', $boat->getName() . ' was undeleted successfully');
                    }
                } catch (\Exception $e){
                    $this->container->get('session')->getFlashBag()->add('error', $boat->getName() . ' was not deleted successfully, because' . $e->getMessage());
                }
                
                return $this->redirect($this->generateUrl('ZizooCharterBundle_Charter_Boats'));
            }
        }

        return $this->render('ZizooBoatBundle:Boat:delete.html.twig', array(
            'boat'                              => $boat,
            'reservation_requests'              => $reservationRequests,
            'future_reservations'               => $futureReservations,
            'form'                              => $form->createView(),
        ));
        
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    public function boatConfirmPriceAction($id)
    {
        $request            = $this->getRequest();
        $user               = $this->getUser();
        $session            = $this->container->get('session');
        $em                 = $this->getDoctrine()->getManager();
        
        $boat = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat')->find($id);
        //if (!$boat || $boat->getCharter()->getAdminUser()!=$user) {
        if (!$boat || !$boat->getCharter()->getUsers()->contains($user)){
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }
        
        $overlap        = $session->get('overlap_'.$id);
        if (!$overlap){
            return $this->redirect($this->generateUrl('ZizooBoatBundle_Boat_EditPrice', array('id' => $id)));
        }
        
        $requestedIds   = $overlap['requested_reservations'];
        $externalIds    = $overlap['external_reservations'];
        
        if (count($requestedIds)==0 && count($externalIds)==0){
            return $this->redirect($this->generateUrl('ZizooBoatBundle_Boat_EditPrice', array('id' => $id)));
        }
        
        $overlapRequestedReservations = array();
        if (count($requestedIds)>0){
            $overlapRequestedReservations   = $em->getRepository('ZizooReservationBundle:Reservation')->findByIds($requestedIds);
        }
        
        $overlapExternalReservations = array();
        if (count($externalIds)>0){
            $overlapExternalReservations    = $em->getRepository('ZizooReservationBundle:Reservation')->findByIds($externalIds);
        }
        
        $form = $this->createForm(new ConfirmBoatPriceType(), new ConfirmBoatPrice($overlapRequestedReservations));
        if ($request->isMethod('post')){
            $form->bind($request);
            if ($form->isValid()){
                $em                 = $this->getDoctrine()->getManager();
                foreach ($overlapRequestedReservations as $overlapRequestedReservation){
                    //$overlapRequestedReservation->setBoat(null);
                    //$boat->removeReservation($overlapRequestedReservation);
                    //$em->remove($overlapRequestedReservation);
                    $overlapRequestedReservation->setStatus(Reservation::STATUS_DENIED);
                    $em->persist($overlapRequestedReservation);
                }

                foreach ($overlapExternalReservations as $overlapExternalReservation){
                    $overlapExternalReservation->setBoat(null);
                    $boat->removeReservation($overlapExternalReservation);
                    $em->remove($overlapExternalReservation);
                }
                
                return $this->forward('ZizooBoat:Boat:boatPrice', array('id' => $id));
            }
        }
        
        return $this->render('ZizooBoatBundle:Boat:price_confirm.html.twig', array(
            'boat'                              => $boat,
            'form'                              => $form->createView(),
            'overlap_requested_reservations'    => $overlapRequestedReservations,
            'overlap_external_reservations'     => $overlapExternalReservations,
            'from'                              => $overlap['from'],
            'to'                                => $overlap['to'],
            'price'                             => $overlap['price'],
            'type'                              => $overlap['type'],
        ));
    }
    
    /**
     * Update the Boat Pricing
     * 
     * @return Response
     */
    public function boatPriceAction($id)
    {
        $request            = $this->getRequest();
        $session            = $this->container->get('session');
        $boatService        = $this->container->get('boat_service');
        $reservationAgent   = $this->container->get('zizoo_reservation_reservation_agent');
        $user               = $this->getUser();
        $charter            = $user->getCharter();
        
        $boat = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat')->find($id);
        //if (!$boat || $boat->getCharter()->getAdminUser()!=$user) {
        if (!$boat || !$charter->getUsers()->contains($user)){
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }
        
        $reservations   = $boat->getReservation();
        $prices         = $boat->getPrice();
    
        if ($request->isMethod('post')){
            $fromStr    = $request->request->get('date_from', null);
            $toStr      = $request->request->get('date_to', null);
            $p          = $request->request->get('price', null);
            $from       = new \DateTime($fromStr);
            $to         = new \DateTime($toStr);
            $confirmed  = $request->request->get('confirmed', false)=='true';
            
            $type               = $request->request->get('type', 'availability');
            
            $overlapRequestedReservations   = $this->getDoctrine()->getRepository('ZizooReservationBundle:Reservation')->getReservations($charter, null, $boat, $from, $to, array(Reservation::STATUS_REQUESTED));
            $overlapExternalReservations    = $this->getDoctrine()->getRepository('ZizooReservationBundle:Reservation')->getReservations($charter, null, $boat, $from, $to, array(Reservation::STATUS_SELF));
            if (count($overlapRequestedReservations)>0 || count($overlapExternalReservations)>0){

                if (!$confirmed){
                    
                    $requestedIds = array();
                    foreach ($overlapRequestedReservations as $overlapRequestedReservation){
                        $requestedIds[] = $overlapRequestedReservation->getId();
                    }

                    $externalIds = array();
                    foreach ($overlapExternalReservations as $overlapExternalReservation){
                        $externalIds[] = $overlapExternalReservation->getId();
                    }
                    
                    $session->set('overlap_'.$id, array('requested_reservations' => $requestedIds, 'external_reservations' => $externalIds, 'from' => $fromStr, 'to' => $toStr, 'price' => $p, 'type' => $type));
                    return $this->redirect($this->generateUrl('ZizooBoatBundle_Boat_ConfirmBoatPrice', array('id' => $id)));
                }
            }
            
            if ($type=='availability' || $type=='default'){
               
                try {
                    $default = $type=='default';
                    $boatService->addPrice($boat, $from, $to, $p, $default, true);
                } catch (InvalidPriceException $e){
                    $this->container->get('session')->getFlashBag()->add('error', $e->getMessage());
                } catch (DBALException $e){
                    $this->container->get('session')->getFlashBag()->add('error', 'Something went wrong');
                }
                return $this->redirect($this->generateUrl('ZizooBoatBundle_Boat_EditPrice', array('id' => $id)));
            } else if ($type=='unavailability'){
                try {
                    $reservationAgent->makeReservationForSelf($boat, $from, $to, true);
                } catch (InvalidReservationException $e){
                    $this->container->get('session')->getFlashBag()->add('error', $e->getMessage());
                }
                return $this->redirect($this->generateUrl('ZizooBoatBundle_Boat_EditPrice', array('id' => $id)));
            }
        }
        
        $session->remove('overlap_'.$id);
        
        return $this->render('ZizooBoatBundle:Boat:price.html.twig', array(
            'boat'          => $boat,
            'reservations'  => $reservations,
            'prices'        => $prices,
        ));
    }
    
    
    /**
     * Activate/hide a specific boat
     * 
     * @return Response
     */
    public function activeAction($id=null)
    {
        $request            = $this->getRequest();
        $boatService        = $this->container->get('boat_service');
        $user               = $this->getUser();
        $charter            = $user->getCharter();
        
        if (!$id) $id       = $request->request->get('boat_id', null);
        $active             = $request->request->get('active_'.$id, false)=='on';
                
        $boat = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat')->find($id);
        //if (!$boat || $boat->getCharter()->getAdminUser()!=$user) {
        if (!$boat || !$charter->getUsers()->contains($user)){
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }
        
        $boat->setActive($active);
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($boat);
        $em->flush();

        return $this->redirect($this->generateUrl('ZizooCharterBundle_Charter_Boats'));
    }
    

}