<?php

namespace Zizoo\BillingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BillingController extends Controller
{
    public function indexAction()
    {
        $billingService = $this->get('zizoo_billing.billing_service');
        $em = $this->getDoctrine()->getManager();
        
        $charters = $em->getRepository('ZizooCharterBundle:Charter')->findAll();
        
        foreach ($charters as $charter){
            $payout = $billingService->createPayout($charter);
            if ($payout === null){
                
            } else if (!$payout instanceof \Zizoo\BillingBundle\Entity\Payout){
                return new \Symfony\Component\HttpFoundation\Response($payout->getMessage());
            } 
        }
        
        
        
    }
}
