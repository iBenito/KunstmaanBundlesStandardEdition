<?php

namespace Zizoo\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class ProfileController extends Controller
{
    /**
     * Get User Information
     * 
     * @param integer $id
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function showAction($id) 
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository('ZizooUserBundle:User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User with id: '.$id);
        }

        return $this->render('ZizooUserBundle:Profile:show.html.twig', array(
            'user' => $user
        ));
    }
    
}

?>
