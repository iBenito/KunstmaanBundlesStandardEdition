<?php

namespace Zizoo\BoatBundle\Controller;

use Zizoo\AddressBundle\Entity\BoatAddress;
use Zizoo\BoatBundle\Form\Type\BoatDetailsType;
use Zizoo\BoatBundle\Form\Type\BookBoatType;
use Zizoo\BoatBundle\Form\Type\ConfirmBoatPriceType;
use Zizoo\BoatBundle\Form\Type\AvailabilityType;
use Zizoo\BoatBundle\Form\Model\BookBoat;
use Zizoo\BoatBundle\Form\Model\ConfirmBoatPrice;
use Zizoo\BoatBundle\Form\Model\Availability;
use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\ReservationBundle\Exception\InvalidReservationException;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Zizoo\BoatBundle\Entity\Boat;

use Zizoo\BoatBundle\Entity\BoatImage;
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
        $map->setHtmlContainerId('map');
        $map->setAsync(true);
        $map->setAutoZoom(false);
        $boatAddress = $boat->getAddress();
        if ($boatAddress->getLat() && $boatAddress->getLng()){
            $map->setCenter($boatAddress->getLat(), $boatAddress->getLng(), true);
        }
        $map->setMapOption('zoom', 6);
        $map->setMapOption('disableDefaultUI', true);
        $map->setMapOption('zoomControl', true);
        $map->setMapOption('scrollwheel', false);
        $map->setStylesheetOptions(array(
            'width' => '100%',
            'height' => '0'
        ));

        if ($boatAddress->getLat() && $boatAddress->getLng()){
            $marker = $this->get('ivory_google_map.marker');
            $marker->setPosition($boatAddress->getLat(), $boatAddress->getLng(), true);
            $marker->setOption('title', $boat->getName());
            $marker->setOption('clickable', true);
            $marker->setIcon($this->container->get('templating.helper.assets')->getUrl('/bundles/zizoobase/images/google-maps-marker.png'));

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
                
        $form = $this->createForm('zizoo_boat_book', $bookBoat, array('label' => '.'));
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
        
        $bookingsAllowed = false;
        if ($this->container->hasParameter('zizoo_booking.allow_bookings')){
            $bookingsAllowed = $this->container->getParameter('zizoo_booking.allow_bookings') === true;
        }
        
        $bookUrl = $this->generateUrl('ZizooBoatBundle_Boat_Show', array('id' => $boat->getId()));
        if ($bookingsAllowed===true){
            $bookUrl = $this->generateUrl('ZizooBookingBundle_book');
        }
        
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
            'book_url'              => $bookUrl,
            'bookings_allowed'      => $bookingsAllowed
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
        $request    = $this->getRequest();
        $user       = $this->getUser();
        $charter    = $user->getCharter();
        $session    = $this->get('session');
        
        $routes         = $request->query->get('routes');
        
        $session->set('step', 'one');
        
        $boat = new Boat(new BoatAddress($charter->getAddress()));
        $boat->setHasDefaultPrice(false);
        $boat->setHasMinimumDays(false);
        
        $form = $this->createForm(new BoatType(), $boat, array('validation_groups' => array('boat_create'), 'required' => false));
        
        if ($request->isMethod('post')){
            $form->bind($request);

            if ($form->isValid()) {
                
                $boat = $form->getData();
                
                $boat->setCharter($charter);

                /* Boat creation is done by Boat Service class */
                $boatService = $this->get('boat_service');
                $boatCreated = $boatService->createBoat($boat, $boat->getAddress(), $boat->getBoatType(), $charter, null, true);

                $overrideUrl = $request->request->get('override_url', null);
                if ($overrideUrl){
                    $url = $this->generateUrl($overrideUrl, array('id' => $boatCreated->getId()));
                } else {
                    $url = $this->generateUrl($routes['details_route'], array('id' => $boatCreated->getId()));
                } 
                
                return $this->redirect($url);
            }
        }
        
        
        return $this->render('ZizooBoatBundle:Boat:new_edit.html.twig', array(
            'boat'          => $boat,
            'form'          => $form->createView(),
            'routes'        => $routes
        ));
    }


    /**
     * Displays a form to edit an existing Boat entity.
     *
     */
    public function editAction($id)
    {
        $request    = $this->getRequest();
        $session    = $this->get('session');
        $em         = $this->getDoctrine()->getManager();
        $boat       = $em->getRepository('ZizooBoatBundle:Boat')->find($id);

        if (!$boat){
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }
        
        $routes         = $request->query->get('routes');
        
        $step = $session->get('step');
        if ($step){
            $session->set('step', 'one');
        }
        $validationGroup = $step?'boat_create':'boat_edit';
        
        $form = $this->createForm(new BoatType(), $boat, array( 'validation_groups'     => array($validationGroup), 
                                                                'required'              => $step===null));
        
        if ($request->isMethod('post')){
            $form->bind($request);
            
            if ($form->isValid()) {
                
                
                $boat = $form->getData();
                
                $em->persist($boat);
                $em->flush();

                $overrideUrl = $request->request->get('override_url', null);
                if ($overrideUrl){
                    $url = $overrideUrl;
                } else if ($step){
                    $url = $this->generateUrl($routes['details_route'], array('id' => $id));
                } else {
                    $url = $this->generateUrl($routes['edit_route'], array('id' => $id));
                }
                
                return $this->redirect($url);
            }
        }

        return $this->render('ZizooBoatBundle:Boat:new_edit.html.twig', array(
            'boat'              => $boat,
            'routes'            => $routes,
            'form'              => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Boat entity.
     *
     */
    public function editDetailsAction($id)
    {
        $request = $this->getRequest();
        $em     = $this->getDoctrine()->getManager();
        $boat   = $em->getRepository('ZizooBoatBundle:Boat')->find($id);

        if (!$boat){
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }
        
        $routes         = $request->query->get('routes');
        
        $session = $this->get('session');
        $step = $session->get('step');
        if ($step){
            $session->set('step', 'two');
        }
        $validationGroup = $step?'boat_create':'boat_details';    
        
        $form = $this->createForm(new BoatDetailsType(), $boat, array('validation_groups' => array($validationGroup), 'required' => $step===null));

        if ($request->isMethod('post')){
            $form->bind($request);
            
            if ($form->isValid()) {
                                
                $boat = $form->getData();
                
                $em->persist($boat);
                $em->flush();


                $overrideUrl = $request->request->get('override_url', null);
                if ($overrideUrl){
                    $url = $overrideUrl;
                } else if ($step){
                    $url = $this->generateUrl($routes['photos_route'], array('id' => $id));
                } else {
                    $url = $this->generateUrl($routes['details_route'], array('id' => $id));
                }
                
                return $this->redirect($url);
            }
        }
        
        return $this->render('ZizooBoatBundle:Boat:edit_details.html.twig', array(
            'boat'              => $boat,
            'routes'            => $routes,
            'form'              => $form->createView()
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

        $routes         = $request->query->get('routes');
        
        $session = $this->get('session');
        $step = $session->get('step');
        if ($step){
            $session->set('step', 'three');
        }
        $validationGroup = $step?'boat_create':'boat_photos';  
        
        $imagesForm = $this->createForm($this->get('zizoo_boat.boat_image_type'), $boat, array('boat_id' => $boat->getId(), 'validation_groups' => array($validationGroup)));

        if ($request->isMethod('post')){
            $imagesForm->bind($request);

            if ($imagesForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $boat = $imagesForm->getData();

                //setting the updated field manually for file upload DO NOT REMOVE
                $boat->setUpdated(new \DateTime());

                $em->persist($boat);

                $em->flush();
                
                $overrideUrl = $request->request->get('override_url', null);
                if ($overrideUrl){
                    $url = $overrideUrl;
                } else if ($step){
                    $url = $this->generateUrl($routes['calendar_route'], array('id' => $id));
                } else {
                    $url = $this->generateUrl($routes['photos_route'], array('id' => $id));
                }
    
                return $this->redirect($url);
            }
        }

        return $this->render('ZizooBoatBundle:Boat:edit_photos.html.twig', array(
            'boat'              => $boat,
            'routes'            => $routes,
            'form'              => $imagesForm->createView()
        ));

    }

    public function addPhotoAction(Request $request, $id)
    {          
        $boat = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat')->find($id);
        if (!$boat){
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }

        $imageFile = $request->files->get('boatFile');
        $boatService = $this->container->get('boat_service');

        try {
            $image = $boatService->addImage($boat, $imageFile, true);
            return new JsonResponse(array('message' => 'Your image has been uploaded successfully', 'id' => $image->getId()));
        } catch (\Exception $e){
            return new Response($e->getMessage(), 400);
        }
    }
    
    public function getPhotosAction(Request $request, $id)
    {
        try {
            $request = $this->getRequest();
            
            $boat = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat')->find($id);
            if (!$boat){
                throw $this->createNotFoundException('Unable to find Boat entity.');
            }

            $routes = $request->request->get('routes');
            
            $imagesForm = $this->createForm($this->get('zizoo_boat.boat_image_type'), $boat);

            return $this->render('ZizooBoatBundle:Boat:edit_photos.html.twig', array(
                'boat'          => $boat,
                'routes'        => $routes,
                'form'          => $imagesForm->createView()
            ));
        } catch (\Exception $e){
            return new Response('Unable to get photos because: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Displays a form to edit an existing Boat entity.
     *
     */
    public function editPriceAction($id)
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

        $step = $session->get('step');
        if ($step){
            $session->set('step', 'four');
        }

        $availability = new Availability($boat);
        $availability->setBoatId($boat->getId());
        $availabilityForm = $this->createForm('zizoo_boat_availability', $availability, array('boat' => $boat, 'label' => 'f'));
        
        $routes = $request->query->get('routes');
        
        $reservations   = $boat->getReservation();
        $prices         = $boat->getPrice();
            
        if ($request->isMethod('post')){
            $availabilityForm->bind($request);
            $availability                   = $availabilityForm->getData();
            $reservationRange               = $availability->getReservationRange();
            $from                           = $reservationRange->getReservationFrom();
            $to                             = $reservationRange->getReservationTo();
            
            // Store date range in session
            if ($from instanceof \DateTime && $to instanceof \DateTime){
                $session->set('availability_from_'.$boat->getId(), $from);
            } else {
                $session->set('availability_from_'.$boat->getId(), null);
            }
            if ($availabilityForm->isValid()){
                
                $overlapRequestedReservations   = $availability->getOverlappingReservationRequests();
                $overlapExternalReservations    = $availability->getOverlappingExternalReservations();
                
                if ($availability->getConfirm()!==null){
                    $em = $this->getDoctrine()->getManager();
                    $reservationAgent = $this->get('zizoo_reservation_reservation_agent');
                    foreach ($overlapRequestedReservations as $overlapRequestedReservation){
                        $reservationAgent->denyReservation($overlapRequestedReservation, false);
                    }

                    foreach ($overlapExternalReservations as $overlapExternalReservation){
                        $overlapExternalReservation->setBoat(null);
                        $boat->removeReservation($overlapExternalReservation);
                        $em->remove($overlapExternalReservation);
                    }
                }

                if ($availability->getType()=='availability' || $availability->getType()=='default'){
                    try {
                        $default = $availability->getType()=='default';
                        $boatService->addPrice($boat, $from, $to, $availability->getPrice(), $default, true);
                    } catch (InvalidPriceException $e){
                        $this->container->get('session')->getFlashBag()->add('error', $e->getMessage());
                    } catch (DBALException $e){
                        $this->container->get('session')->getFlashBag()->add('error', 'Something went wrong');
                    }
                    return $this->redirect($this->generateUrl($routes['calendar_route'], array('id' => $id), $request->query->all()));
                } else if ($availability->getType()=='unavailability'){
                    try {
                        $reservationAgent->makeReservationForSelf($boat, $from, $to, true);
                    } catch (InvalidReservationException $e){
                        $this->container->get('session')->getFlashBag()->add('error', $e->getMessage());
                    }
                    return $this->redirect($this->generateUrl($routes['calendar_route'], array('id' => $id), $request->query->all()));
                }
            }
            
        }
        $v = $availabilityForm->createView();
        $a = "";
        return $this->render('ZizooBoatBundle:Boat:edit_price.html.twig', array(
            'boat'              => $boat,
            'reservations'      => $reservations,
            'prices'            => $prices,
            'routes'            => $routes,
            'form'              => $availabilityForm->createView()
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
        
        $routes         = $request->query->get('routes');
        
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
        
        $canBeDeleted = $boatService->canDeleteBoat($boat);
        
        if ($request->isMethod('post')){
            $form->bind($request);

            if ($form->isValid()) {

                try {
                    
                    $delete = $boat->getDeleted()==null;
                    if ($delete && $canBeDeleted){
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
                    $this->container->get('session')->getFlashBag()->add('error', $boat->getName() . ' was not deleted successfully, because ' . $e->getMessage());
                }
                
                return $this->redirect($this->generateUrl($routes['complete_route']));
            }
        }

        return $this->render('ZizooBoatBundle:Boat:delete.html.twig', array(
            'boat'                              => $boat,
            'reservation_requests'              => $reservationRequests,
            'future_reservations'               => $futureReservations,
            'form'                              => $form->createView(),
            'routes'                            => $routes,
            'can_be_deleted'                    => $canBeDeleted
        ));
        
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
   
    /**
     * Activate/hide a specific boat
     * 
     */
    public function activeAction($id=null)
    {
        $request            = $this->getRequest();
        $boatService        = $this->container->get('boat_service');
        $user               = $this->getUser();
        $charter            = $user->getCharter();
        $em                 = $this->getDoctrine()->getEntityManager();
        
        if (!$id) $id       = $request->get('boat_id', null);
        $active             = $request->get('active', false)=='true';
                
        $boat = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat')->find($id);
        if (!$boat || !$charter->getUsers()->contains($user)){
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }
        
        $response = new JSONResponse();
        
        $completeErrors = $boatService->boatCompleteValidate($boat);
        if ( ($active === true && count($completeErrors) == 0) || $active !== true){
            $boat->setActive($active);
            $em->persist($boat);
            $em->flush();
        } else {
            $response->setStatusCode(400);
        }
        
        $response->setData(array(
            'boat_id'       => $boat->getId(),
            'boat_active'   => $boat->getActive(),
            'errors'        => $completeErrors
        ));
        
        $routes = $request->get('routes');
 
        if (true){
            return $response;
        } else {
            return $this->render('ZizooBoatBundle:Boat:active.html.twig', array(
                'boat'              => $boat,
                'errors'            => $completeErrors,
                'routes'            => $routes
            ));
        }
        
    }


}