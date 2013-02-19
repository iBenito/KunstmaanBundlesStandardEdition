<?php

namespace Zizoo\BillingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BillingController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ZizooBillingBundle:Billing:index.html.twig', array('name' => $name));
    }
}
