<?php

namespace Zizoo\AddressBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AddressController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ZizooAddressBundle:Address:index.html.twig', array('name' => $name));
    }
}
