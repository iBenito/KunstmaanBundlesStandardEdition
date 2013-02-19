<?php

namespace Zizoo\BaseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Zizoo\BaseBundle\Entity\Enquiry;
use Zizoo\BaseBundle\Form\EnquiryType;

use Zizoo\ProfileBundle\Form\ProfileType;

use Zizoo\BoatBundle\Entity\Boat;

use Zizoo\BillingBundle\Form\Model\BankAccount;
use Zizoo\BillingBundle\Form\Type\BankAccountType;

class DashboardController extends Controller {

    /**
     * Displays Mini User Profile and Navigation
     * 
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function userWidgetAction()
    {
        $user = $this->getUser();
        
        return $this->render('ZizooBaseBundle::user_widget.html.twig', array(
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
        
        return $this->render('ZizooBaseBundle:Dashboard/Boat:new.html.twig', array(
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
        
        return $this->render('ZizooBaseBundle:Dashboard/Boat:edit.html.twig', array(

        ));
    }
    
    /**
     * Edit existing Boat
     * 
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function boatPhotosAction()
    {
        
        return $this->render('ZizooBaseBundle:Dashboard/Boat:photos.html.twig', array(

        ));
    }
    
    /**
     * Edit existing Boat
     * 
     * @author Benito Gonzalez <vbenitogo@gmail.com>
     */
    public function boatPriceAction()
    {
        
        return $this->render('ZizooBaseBundle:Dashboard/Boat:price.html.twig', array(

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
    public function settingsAction(Request $request)
    {
        // Include Braintree API
        require_once $this->container->getParameter('braintree_path').'/lib/Braintree.php';
        \Braintree_Configuration::environment($this->container->getParameter('braintree_environment'));
        \Braintree_Configuration::merchantId($this->container->getParameter('braintree_merchant_id'));
        \Braintree_Configuration::publicKey($this->container->getParameter('braintree_public_key'));
        \Braintree_Configuration::privateKey($this->container->getParameter('braintree_private_key'));
        
        $userService        = $this->container->get('zizoo_user_user_service');
        $trans              = $this->get('translator');
        
        $user               = $this->getUser();
        $braintreeCustomer  = $userService->getPaymentUser($user);
        
        if ($braintreeCustomer){
            if ($request->isMethod('POST')){
                
                $form = $this->createForm(new BankAccountType());
                $form->bindRequest($request);
                
                if ($form->isValid()){
                    $bankAccount = $form->getData();

                    $updateResult = \Braintree_Customer::update(
                        $braintreeCustomer->id,
                        array(
                          'customFields' => array('iban' => $bankAccount->getIBAN(), 'bic' => $bankAccount->getBIC())
                      )
                    );

                    if ($updateResult->success){
                        $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_billing.bank_account_changed'));
                        return $this->redirect($this->generateUrl('ZizooBaseBundle_dashboard_account'));
                    } else {
                        $this->get('session')->getFlashBag()->add('error', $trans->trans('zizoo_billing.bank_account_not_changed'));
                    }
                }
                        
            } else {
                
                $bankAccount = new BankAccount();
                if (is_array($braintreeCustomer->customFields)){
                    if (array_key_exists('iban', $braintreeCustomer->customFields)){
                        $bankAccount->setIBAN($braintreeCustomer->customFields['iban']);
                    }
                    if (array_key_exists('bic', $braintreeCustomer->customFields)){
                        $bankAccount->setBIC($braintreeCustomer->customFields['bic']);
                    }
                }
                $form = $this->createForm(new BankAccountType(), $bankAccount);
                
            }
        } else {
            $form = null;
        }
        
        return $this->render('ZizooBaseBundle:Dashboard:settings.html.twig', array(
                    'form'              => $form?$form->createView():null,
                    'braintree_valid'   => $braintreeCustomer!=null
        ));
    }

}