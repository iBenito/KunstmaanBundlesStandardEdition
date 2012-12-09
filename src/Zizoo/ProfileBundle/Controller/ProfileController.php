<?php

namespace Zizoo\ProfileBundle\Controller;

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
    public function showAction() 
    {
        $user = $this->getUser();

        return $this->render('ZizooProfileBundle:Profile:show.html.twig', array(
            'user' => $user
        ));
    }
    
}

?>
