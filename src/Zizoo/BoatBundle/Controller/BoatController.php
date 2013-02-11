<?php

namespace Zizoo\BoatBundle\Controller;

use Zizoo\BoatBundle\Form\Type\BookBoatType;
use Zizoo\BoatBundle\Form\Model\BookBoat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

use Zizoo\BoatBundle\Entity\Boat;
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
    /**
     * Create form for Boat
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
                    array('from_folder' => 'attachments/' . $boat->getId(), 
                      'to_folder' => 'tmp/attachments/' . $editId,
                      'create_to_folder' => true));
            }
        }
        $existingFiles = $this->get('punk_ave.file_uploader')->getFiles(array('folder' => 'tmp/attachments/' . $editId));
        
        return $this->render('ZizooBoatBundle:Boat:boat_form_widget.html.twig', array(
            'boat' => $boat,
            'form' => $form->createView(),
            'formAction' => $formAction,
            'existingFiles' => $existingFiles,
            'editId' => $editId,
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
        $boat = new Profile();
        $form = $this->createForm(new BoatType(), $boat);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($boat);
            $em->flush();

            return $this->redirect($this->generateUrl('ZizooBoatBundle_show', array('id' => $boat->getId())));
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