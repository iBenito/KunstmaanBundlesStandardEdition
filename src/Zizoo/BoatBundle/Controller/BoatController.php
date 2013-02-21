<?php

namespace Zizoo\BoatBundle\Controller;

use Zizoo\BoatBundle\Form\Type\BookBoatType;
use Zizoo\BoatBundle\Form\Model\BookBoat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Entity\Image;
use Zizoo\BoatBundle\Form\BoatType;

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
        
        
        $map = $this->get('ivory_google_map.map');
        $map->setAsync(true);
        $map->setAutoZoom(false);
        $boatAddress = $boat->getAddress();
        $map->setCenter($boatAddress->getLat(), $boatAddress->getLng(), true);
        $map->setMapOption('zoom', 4);
        $map->setMapOption('disableDefaultUI', true);
        $map->setMapOption('zoomControl', true);
        $map->setStylesheetOptions(array(
            'width' => '100%',
            'height' => '300px'
        ));
        
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
        
        
        
        $request = $this->getRequest();       
        $request->query->set('url', $this->generateUrl('ZizooBoatBundle_show', array('id' => $id)));
        $request->query->set('ajax_url', $this->generateUrl('ZizooBoatBundle_booking_widget', array('id' => $id, 'request' => $request)));
        return $this->render('ZizooBoatBundle:Boat:show.html.twig', array(
            'boat'      => $boat,
            'map'       => $map,
            'request'   => $request
        ));
    }
    
    
    
    public function bookingWidgetAction($id, Request $request){
        $session = $request->getSession();
        $em = $this->getDoctrine()->getEntityManager();
        $boat = $em->getRepository('ZizooBoatBundle:Boat')->find($id);
        if (!$boat) {
            throw $this->createNotFoundException('Unable to find boat post.');
        }  
        
        $form = $this->createForm(new BookBoatType(), new BookBoat($id));
        $form->bindRequest($request);
        $bookBoat = $form->getData();
        
        $reservationAgent = $this->get('zizoo_reservation_reservation_agent');
        $reservationExists = $reservationAgent->reservationExists($boat, $bookBoat->getReservationFrom(), $bookBoat->getReservationTo());
        
        $totalPrice = $reservationAgent->getTotalPrice($boat, $bookBoat->getReservationFrom(), $bookBoat->getReservationTo());
        
        $valid = false;
        
        if ($form->isValid() && !$reservationExists && $bookBoat->getNumGuests()>0){
            $valid = true;
            $session->set('boat', $bookBoat);
        } else {
            $valid = false;
            $session->remove('boat');
        }
        
        $url            = $request->query->get('url', null);
        $ajaxUrl        = $request->query->get('ajax_url', null);
        if (!$url)      $url = $request->request->get('url', null);
        if (!$ajaxUrl)  $ajaxUrl = $request->request->get('ajax_url');
        
        return $this->render('ZizooBoatBundle:Boat:booking_widget.html.twig', array(
            'boat'                  => $boat,
            'book_boat'             => $bookBoat,
            'total_price'           => $totalPrice,
            'form'                  => $form->createView(),
            'reservation_exists'    => $reservationExists,
            'valid'                 => $valid,
            'url'                   => $url,
            'ajax_url'              => $ajaxUrl,
            'book_url'              => $this->generateUrl('ZizooBookingBundle_book')
        ));
    }
    
    /**
     * Create input form for Boat
     *
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function boatFormWidgetAction(Boat $boat, $formAction)
    {
        $form = $this->createForm(new BoatType(), $boat);

        // The Punk Ave file uploader part of the Form
        $editId = $this->getRequest()->get('editId');
        if (!preg_match('/^\d+$/', $editId))
        {
            $editId = sprintf('%09d', mt_rand(0, 1999999999));
            if ($boat->getId())
            {
                $this->get('punk_ave.file_uploader')->syncFiles(
                    array('from_folder' => '../images/boats/' . $boat->getId(), 
                      'to_folder' => 'tmp/attachments/' . $editId,
                      'create_to_folder' => true));
            }
        }
        $existingFiles = $this->get('punk_ave.file_uploader')->getFiles(array('folder' => 'tmp/attachments/' . $editId));
        
        /** @var Ivory\GoogleMapBundle\Model\Map */
        $map = $this->get('ivory_google_map.map');
        $map->setAsync(true);
        $map->setAutoZoom(false);
        $map->setCenter(45, 15, true);
        $map->setMapOption('zoom', 4);
        $map->setMapOption('disableDefaultUI', true);
        $map->setMapOption('zoomControl', true);
        $map->setStylesheetOptions(array(
            'width' => '100%',
            'height' => '300px'
        ));
        
        $em = $this->getDoctrine()->getEntityManager();
        $marinas = $em->getRepository('ZizooAddressBundle:Marina')->getAllMarinas();
  
        foreach ($marinas as $marina){
            $marker = $this->get('ivory_google_map.marker');
            $marker->setPosition($marina->getLat(), $marina->getLng(), true);
            $marker->setOption('title', $marina->getName());
            $marker->setOption('clickable', true);
            $marker->setIcon('http://www.incrediblue.com/assets/map-pin.png');
            
            $map->addMarker($marker);
        }
        
        return $this->render('ZizooBoatBundle:Boat:boat_form_widget.html.twig', array(
            'boat' => $boat,
            'form' => $form->createView(),
            'formAction' => $formAction,
            'existingFiles' => $existingFiles,
            'editId' => $editId,
            'map' => $map,
        ));
    }
    
    /**
     * Uploads Images.
     *
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function uploadAction()
    {
        $editId = $this->getRequest()->get('editId');
        if (!preg_match('/^\d+$/', $editId))
        {
            throw new Exception("Bad edit id");
        }

        $this->get('punk_ave.file_uploader')->handleFileUpload(array('folder' => 'tmp/attachments/' . $editId));
    }
    
    /**
     * Displays a form to create a new Boat entity.
     *
     */
    public function newAction()
    {
        $boat = new Boat();

        return $this->render('ZizooBoatBundle:Boat:new.html.twig', array(
            'boat' => $boat,
            'formAction' => 'ZizooBoatBundle_create'
        ));
    }

    /**
     * Creates a new Boat entity.
     *
     */
    public function createAction(Request $request)
    {
        $boat = new Boat();
        $form = $this->createForm(new BoatType(), $boat);
        $form->bind($request);

        $editId = $this->getRequest()->get('editId');
        if (!preg_match('/^\d+$/', $editId))
        {
            throw new Exception("Bad edit id");
        }
        
        if ($form->isValid()) {
            $boat->setUser($this->getUser());
            
            $fileUploader = $this->get('punk_ave.file_uploader');
            
            /* Get a list of uploaded images to add to Boat */
            $files = $fileUploader->getFiles(array('folder' => '/tmp/attachments/' . $editId));
            $images = array();
            foreach ($files as $file) {
                $image = new Image();
                $image->setBoat($boat);
                $image->setPath($file);
                $images[] = $image;
            }
                    
            /* Boat creation is done by Boat Service class */
            $boatService = $this->get('boat_service');
            $boatCreated = $boatService->createBoat($boat, $boat->getAddress(), $boat->getBoatType(), null, $images);

            $fileUploader->syncFiles(
                array('from_folder' => '/tmp/attachments/' . $editId,
                'to_folder' => '../images/boats/' . $boatCreated->getId(),
                'remove_from_folder' => true,
                'create_to_folder' => true));
            
            return $this->redirect($this->generateUrl('ZizooBoatBundle_show', array('id' => $boatCreated->getId())));
        }

        return $this->render('ZizooBoatBundle:Boat:new.html.twig', array(
            'boat' => $boat,
            'form' => $form->createView(),
            'formAction' => 'ZizooBoatBundle_create'
        ));
    }

    /**
     * Displays a form to edit an existing Boat entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $boat = $em->getRepository('ZizooBoatBundle:Boat')->find($id);

        if (!$boat) {
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ZizooBoatBundle:Boat:edit.html.twig', array(
            'boat'      => $boat,
            'delete_form' => $deleteForm->createView(),
            'formAction' => 'ZizooBoatBundle_update'
        ));
    }

    /**
     * Edits an existing Boat entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $boat = $em->getRepository('ZizooBoatBundle:Boat')->find($id);

        if (!$boat) {
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new BoatType(), $boat);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($boat);
            $em->flush();

            return $this->redirect($this->generateUrl('ZizooBoatBundle_edit', array('id' => $id)));
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
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $boat = $em->getRepository('ZizooBoatBundle:Boat')->find($id);

            if (!$boat) {
                throw $this->createNotFoundException('Unable to find Boat entity.');
            }

            $em->remove($boat);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ZizooBaseBundle_dashboard_boats'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

}