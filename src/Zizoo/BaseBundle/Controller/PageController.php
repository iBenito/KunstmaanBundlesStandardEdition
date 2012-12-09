<?php

namespace Zizoo\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zizoo\BaseBundle\Entity\Enquiry;
use Zizoo\BaseBundle\Form\EnquiryType;

class PageController extends Controller {

    public function indexAction() 
    {
        $user = null;
        $em = $this->getDoctrine()->getEntityManager();

        $boats = $em->getRepository('ZizooBoatBundle:Boat')->getBoats();
                
        $securityContext = $this->container->get('security.context');
        if( $securityContext->isGranted('IS_AUTHENTICATED_FULLY') || $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            $user = $this->getUser();
        }
        
        return $this->render('ZizooBaseBundle:Page:index.html.twig',array(
            'user' => $user,
            'boats' => $boats
        ));
    }
    
    public function aboutAction()
    {
        return $this->render('ZizooBaseBundle:Page:about.html.twig');
    }

}