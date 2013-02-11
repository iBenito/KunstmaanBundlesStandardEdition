<?php

namespace Zizoo\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zizoo\BaseBundle\Entity\Enquiry;
use Zizoo\BaseBundle\Form\EnquiryType;

use Zizoo\ProfileBundle\Form\ProfileType;

use Zizoo\BoatBundle\Entity\Boat;

class DashboardController extends Controller {

    /**
     * Displays Mini User Profile and Navigation
     * 
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function userWidgetAction()
    {
        $user = $this->getUser();
        
        return $this->render('ZizooBaseBundle:Dashboard:user_widget.html.twig', array(
            'user' => $user,
            'owner' => 'Owner',
            'skipper' => 'Skipper'
        ));
    }
    
    /**
     * Display User Dashboard
     * 
     * @param integer $userId
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function indexAction()
    {
        return $this->render('ZizooBaseBundle:Dashboard:index.html.twig', array(

        ));
    }
    
    /**
     * Display User Profile
     * 
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function profileAction()
    {
        $user = $this->getUser();
        $profile = $user->getProfile();
      
        if (!$profile) {
            throw $this->createNotFoundException('Unable to find Profile entity.');
        }
      
        return $this->render('ZizooBaseBundle:Dashboard:profile.html.twig',array(
            'profile' => $profile,
            'formPath' => $this->getRequest()->get('_route')
        ));

    }
    
    /**
     * Display User Inbox
     * 
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function inboxAction()
    {
        return $this->render('ZizooBaseBundle:Dashboard:inbox.html.twig', array(

        ));
    }
    
    /**
     * Display User Boats
     * 
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function boatsAction()
    {
        $user = $this->getUser();
        $boats = $user->getBoats();

        return $this->render('ZizooBaseBundle:Dashboard:boats.html.twig', array(
            'boats' => $boats
        ));
    }
    
    /**
     * Add new Boat
     * 
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function boatNewAction()
    {
        $boat = new Boat();
        
        return $this->render('ZizooBaseBundle:DashboardBoat:new.html.twig', array(
            'boat' => $boat,
            'formAction' => 'ZizooBoatBundle_create'
        ));
    }
    
    /**
     * Edit existing Boat
     * 
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function boatEditAction()
    {
        
        return $this->render('ZizooBaseBundle:DashboardBoat:edit.html.twig', array(

        ));
    }
    
    /**
     * Edit existing Boat
     * 
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function boatPhotosAction()
    {
        
        return $this->render('ZizooBaseBundle:DashboardBoat:photos.html.twig', array(

        ));
    }
    
    /**
     * Edit existing Boat
     * 
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function boatPriceAction()
    {
        
        return $this->render('ZizooBaseBundle:DashboardBoat:price.html.twig', array(

        ));
    }
    
    /**
     * Display User Skills
     * 
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function skillsAction()
    {
        
        return $this->render('ZizooBaseBundle:Dashboard:skills.html.twig', array(

        ));
    }
    
    /**
     * Display User Bookings
     * 
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function tripsAction()
    {
        
        return $this->render('ZizooBaseBundle:Dashboard:trips.html.twig', array(
        ));
    }
    
    /**
     * Display User Account Settings
     * 
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function settingsAction()
    {
        
        return $this->render('ZizooBaseBundle:Dashboard:settings.html.twig', array(
                    
        ));
    }

}