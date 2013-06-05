<?php

namespace Zizoo\CharterBundle\Controller;

use Zizoo\CharterBundle\Form\Type\CharterRegistrationType;
use Zizoo\CharterBundle\Entity\CharterRepository;

use Zizoo\BillingBundle\Form\Type\PayoutSettingsType;
use Zizoo\BillingBundle\Form\Model\PayoutSettings;
use Zizoo\BillingBundle\Form\Type\BankAccountType;
use Zizoo\BillingBundle\Form\Model\BankAccount;
use Zizoo\BillingBundle\Form\Type\PayPalType;
use Zizoo\BillingBundle\Form\Model\PayPal;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CharterController extends Controller
{
    /**
     * Get Charter Information
     * 
     * @return Response
     */
    public function showAction($id) 
    {
        $charter    = $this->getDoctrine()->getManager()->getRepository('ZizooCharterBundle:Charter')->findOneById($id);

        return $this->render('ZizooCharterBundle:Charter:show.html.twig', array(
            'charter' => $charter
        ));
    }
    
    /**
     * Display Charter Boats
     * 
     * @return Response
     */
    public function showBoatsAction($id, $page=1)
    {
        $pageSize   = 3;
        $charter    = $this->getDoctrine()->getManager()->getRepository('ZizooCharterBundle:Charter')->findOneById($id);
        $boats      = $this->getDoctrine()->getManager()->getRepository('ZizooBoatBundle:Boat')->getLatestCharterBoats($charter, $pageSize+1, $page);
        
        return $this->render('ZizooCharterBundle:Charter:show_boats.html.twig', array(
            'charter'      => $charter,
            'boats'         => $boats,
            'page'          => $page,
            'page_size'     => $pageSize
        ));
    }
    
    public function boatsAction(Request $request)
    {
        $user       = $this->getUser();
        $charter    = $user->getCharter();
        $boats      = $charter->getBoats();
        
        $sort               = $request->query->get('sort', 'b.id');
        $dir                = $request->query->get('direction', 'desc');
        $searchBoatName     = $request->query->get('boat_name', null);
        $searchBoatType     = $request->query->get('boat_type', null);
        $page               = $this->get('request')->query->get('page', 1);
        $pageSize           = $this->get('request')->query->get('page_size', 25);
        
        $em    = $this->getDoctrine()->getEntityManager();
        //$dql   = "SELECT b, c FROM ZizooBoatBundle:Boat b, ZizooCharterBundle:Charter c WHERE ";
        $dql = 'SELECT b, c FROM ZizooBoatBundle:Boat b JOIN b.charter c WHERE c.id = '.$charter->getId();
        
        if ($searchBoatName) {
            $dql .= " AND (b.name LIKE '%".$searchBoatName."%' OR b.title LIKE '%".$searchBoatName."%')";
        }
        
        if ($searchBoatType){
            $dql .= " AND b.boatType = '" . $searchBoatType . "'";
        }
        
        if ($sort && $dir){
            $dql .= " ORDER BY " . $sort . " " . $dir;
        }
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            $pageSize/*limit per page*/
        );
        
        return $this->render('ZizooCharterBundle:Charter:boats.html.twig', array(
            'pagination'        => $pagination,
            'direction'         => $dir,
            'sort'              => $sort,
            'page'              => $page,
            'page_size'         => $pageSize,
            'request_uri'       => $request->getSchemeAndHttpHost().$request->getRequestUri(),
            'search_boat_name'  => $searchBoatName,
            'search_boat_type'  => $searchBoatType,
            'boat_types'        => $em->getRepository('ZizooBoatBundle:BoatType')->findAll()
        ));
    }
    
    /**
     * Edit Charter Profile
     * 
     * @return Response
     */
    public function profileAction()
    {
        $user       = $this->getUser();
        $charter    = $user->getCharter();
        
        if (!$charter) {
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }
      
        return $this->render('ZizooCharterBundle:Charter:profile.html.twig',array(
            'charter'   => $charter,
            'formPath'  => $this->getRequest()->get('_route')
        ));

    }
    
    
    /**
     * Edit Payout Settings
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return Response
     */
    public function payoutSettingsAction(Request $request)
    {
        // Include Braintree API
        require_once $this->container->getParameter('braintree_path').'/lib/Braintree.php';
        \Braintree_Configuration::environment($this->container->getParameter('braintree_environment'));
        \Braintree_Configuration::merchantId($this->container->getParameter('braintree_merchant_id'));
        \Braintree_Configuration::publicKey($this->container->getParameter('braintree_public_key'));
        \Braintree_Configuration::privateKey($this->container->getParameter('braintree_private_key'));
        
        $userService        = $this->container->get('zizoo_user_user_service');
        $trans              = $this->get('translator');
        $payoutSettingsType = new PayoutSettingsType();
        
        $form = $this->createForm($payoutSettingsType);
        
        $user               = $this->getUser();
        $braintreeCustomer  = $userService->getPaymentUser($user);

        if ($request->isMethod('POST')){
            
            if ($braintreeCustomer){
                $form->bind($request);

                if ($form->isValid()){
                    $payoutSettings = $form->getData();
                    $bankAccount    = $payoutSettings->getBankAccount();
                    $paypal         = $payoutSettings->getPayPal();
                    
                    if ($payoutSettings->getPayoutMethod()=='bank_account'){
                        $updateResult = \Braintree_Customer::update(
                            $braintreeCustomer->id,
                            array(
                              'customFields' => array(  'payout_method' => $payoutSettings->getPayoutMethod(),
                                                        'account_owner' => $bankAccount->getAccountOwner(),
                                                        'bank_name'     => $bankAccount->getBankName(),
                                                        'bank_country'  => $bankAccount->getCountry()->getIso(),
                                                        'iban'          => $bankAccount->getIBAN(), 
                                                        'bic'           => $bankAccount->getBIC())
                          )
                        );
                    } else if ($payoutSettings->getPayoutMethod()=='paypal'){
                        $updateResult = \Braintree_Customer::update(
                            $braintreeCustomer->id,
                            array(
                              'customFields' => array(  'payout_method' => $payoutSettings->getPayoutMethod(),
                                                        'paypal'        => $paypal->getUsername())
                          )
                        );
                    } else {
                        $this->get('session')->getFlashBag()->add('error', $trans->trans('zizoo_billing.payout_settings_not_changed'));
                        return $this->redirect($this->generateUrl('ZizooCharterBundle_Charter_PayoutSettings'));
                    }

                    if ($updateResult->success){
                        $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_billing.payout_settings_changed'));
                        return $this->redirect($this->generateUrl('ZizooCharterBundle_Charter_PayoutSettings'));
                    } else {
                        $this->get('session')->getFlashBag()->add('error', $trans->trans('zizoo_billing.payout_settings_not_changed'));
                    }
                }
                
            }
            
            
        } else {
            if ($braintreeCustomer){
                $bankAccount    = new BankAccount();
                $paypal         = new PayPal();
                $payoutSettings = new PayoutSettings();
                $payoutSettings->setBankAccount($bankAccount);
                $payoutSettings->setPayPal($paypal);
                
                if (is_array($braintreeCustomer->customFields)){
                    if (array_key_exists('payout_method', $braintreeCustomer->customFields)){
                        $payoutSettings->setPayoutMethod($braintreeCustomer->customFields['payout_method']);
                    }
                    if (array_key_exists('account_owner', $braintreeCustomer->customFields)){
                        $bankAccount->setAccountOwner($braintreeCustomer->customFields['account_owner']);
                    }
                    if (array_key_exists('bank_name', $braintreeCustomer->customFields)){
                        $bankAccount->setBankName($braintreeCustomer->customFields['bank_name']);
                    }
                    if (array_key_exists('bank_country', $braintreeCustomer->customFields)){
                        $em = $this->getDoctrine()->getManager();
                        $country = $em->getRepository('ZizooAddressBundle:Country')->findOneByIso($braintreeCustomer->customFields['bank_country']);
                        $bankAccount->setCountry($country);
                    }
                    if (array_key_exists('iban', $braintreeCustomer->customFields)){
                        $bankAccount->setIBAN($braintreeCustomer->customFields['iban']);
                    }
                    if (array_key_exists('bic', $braintreeCustomer->customFields)){
                        $bankAccount->setBIC($braintreeCustomer->customFields['bic']);
                    }
                    if (array_key_exists('paypal', $braintreeCustomer->customFields)){
                        $paypal->setUsername($braintreeCustomer->customFields['paypal']);
                    }
                }
                
                $form = $this->createForm(new PayoutSettingsType(), $payoutSettings);
               
            }
            
        }
        
        return $this->render('ZizooCharterBundle:Charter:payout_settings.html.twig', array(
                    'form'              => $form?$form->createView():null,
                    'braintree_valid'   => $braintreeCustomer!=null
        ));
    }
    
    
    
    
    
    
    
    
    
}
