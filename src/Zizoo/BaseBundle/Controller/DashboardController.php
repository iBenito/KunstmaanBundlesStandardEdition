<?php

namespace Zizoo\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zizoo\BaseBundle\Entity\Enquiry;
use Zizoo\BaseBundle\Form\EnquiryType;

class DashboardController extends Controller {

    /**
     * Display User Dashboard
     * 
     * @param integer $userId
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function indexAction()
    {
        $user = $this->getUser();
        
        return $this->render('ZizooBaseBundle:Dashboard:index.html.twig', array(
            'user' => $user,
            'profile' => $user->getProfile()
        ));
    }
    
    /**
     * Display User Profile
     * 
     * @param integer $userId
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function profileAction()
    {
        $user = $this->getUser();
        
        return $this->render('ZizooBaseBundle:Dashboard:profile.html.twig', array(
            'user' => $user,
            'profile' => $user->getProfile()
        ));
    }

}