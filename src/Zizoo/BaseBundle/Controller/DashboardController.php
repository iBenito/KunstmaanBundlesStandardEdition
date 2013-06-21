<?php

namespace Zizoo\BaseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;

use Zizoo\BaseBundle\Form\Type\ConfirmBoatPriceType;
use Zizoo\BaseBundle\Form\Model\ConfirmBoatPrice;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Entity\Price;
use Zizoo\BoatBundle\Entity\Image;
use Zizoo\BoatBundle\Form\Type\ImageType;
use Zizoo\BoatBundle\Exception\InvalidPriceException;

use Zizoo\ReservationBundle\Form\Type\DenyReservationType;
use Zizoo\ReservationBundle\Form\Model\DenyReservation;

use Zizoo\CrewBundle\Form\SkillsType;



use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\ReservationBundle\Exception\InvalidReservationException;

/**
 * Dashboard Controller for managind everything related to User account.
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
        //$crew = (count($user->getSkills())) ? true : false;
        return $this->render('ZizooBaseBundle:Dashboard:user_widget.html.twig', array(
            'user'      => $user,
            'route'     => $route,
            'show_user' => $showUser
            //'crew' => $crew
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
     * Add new Boat. Rendering of page will be delegated to Boat bundle.
     *
     * @return Response
     */
    public function boatNewAction()
    {
        $boat = new Boat();

        return $this->render('ZizooBaseBundle:Dashboard/Boat:new.html.twig', array(
            'boat' => $boat,
            'formAction' => 'ZizooBoatBundle_create',
            'formRedirect' => 'ZizooBaseBundle_Dashboard_BoatPhotos'
        ));
    }

    /**
     * Edit existing Boat
     *
     * @param integer $id Boat Id
     * @return Response
     */
    public function boatEditAction($id)
    {
        $user   = $this->getUser();
        $boat   = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat')->find($id);
        //if (!$boat || $boat->getCharter()->getAdminUser()!=$user) {
        if (!$boat || !$boat->getCharter()->getUsers()->contains($user)){
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }

        return $this->render('ZizooBaseBundle:Dashboard/Boat:edit.html.twig', array(
            'boat'  => $boat,
            'formAction' => 'ZizooBoatBundle_update',
            'formRedirect' => 'ZizooBoatBundle_edit'
        ));
    }

    /**
     * Add photos to existing Boat
     *
     * @param integer $id Boat Id
     * @return Response
     */
    public function boatPhotosAction($id)
    {
        $user   = $this->getUser();
        $boat   = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat')->find($id);
        //if (!$boat || $boat->getCharter()->getAdminUser()!=$user) {
        if (!$boat || !$boat->getCharter()->getUsers()->contains($user)){
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }

        // The Punk Ave file uploader part of the Form for Uploading Images
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

        $imagesForm = $this->createForm(new ImageType());

        return $this->render('ZizooBaseBundle:Dashboard/Boat:photos.html.twig', array(
            'boat'  => $boat,
            'imagesForm'  => $imagesForm->createView(),
            'existingFiles' => $existingFiles,
            'editId' => intval($editId),
            'formAction' => 'ZizooBoatBundle_update',
            'formRedirect' => 'ZizooBoatBundle_edit'
        ));
    }

    /**
     * Adds Images to Existing Boat
     *
     * @return Response
     */
    public function boatPhotosCreateAction()
    {
        $user   = $this->getUser();
        $boatId = $this->getRequest()->get('boatId');

        $boat = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat')->find($boatId);
        //if (!$boat || $boat->getCharter()->getAdminUser()!=$user) {
        if (!$boat || !$boat->getCharter()->getUsers()->contains($user)){
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }

        $editId = $this->getRequest()->get('editId');
        if (!preg_match('/^\d+$/', $editId))
        {
            throw new Exception("Bad edit id");
        }

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
        $boatService->addImages($boat, new ArrayCollection($images));

        $fileUploader->syncFiles(
            array('from_folder' => '/tmp/attachments/' . $editId,
            'to_folder' => '../images/boats/' . $boatId,
            'remove_from_folder' => true,
            'create_to_folder' => true)
        );

        return $this->redirect($this->generateUrl('ZizooBoatBundle_edit', array('id' => $boatId)));
    }
     
    /**
     * Display User Skills
     * 
     * @return Response
     */
    public function skillsAction()
    {
        $user = $this->getUser();
        
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