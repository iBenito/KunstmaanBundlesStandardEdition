<?php

namespace Zizoo\BaseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Entity\Image;
use Zizoo\BoatBundle\Form\ImageType;

use Zizoo\BillingBundle\Form\Model\BankAccount;
use Zizoo\BillingBundle\Form\Type\BankAccountType;

/**
 * Dashboard Controller for managind everything related to User account.
 *
 * @author Benito Gonzalez <vbenitogo@gmail.com>
 */
class DashboardController extends Controller {

    /**
     * Displays Mini User Profile and Navigation
     * 
     * @return Response
     */
    public function userWidgetAction()
    {
        $user = $this->getUser();

        return $this->render('ZizooBaseBundle::user_widget.html.twig', array(
            'user' => $user,
//            'owner' => (empty($user->getBoats())) ? false : true,
//            'skipper' => (empty($user->getBoats())) ? false : true
        ));
    }
    
    /**
     * Display User Dashboard
     * 
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('ZizooBaseBundle:Dashboard:index.html.twig', array(

        ));
    }
    
    /**
     * Display User Profile
     * 
     * @return Response
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
     * @return Response
     */
    public function inboxAction()
    {
        return $this->render('ZizooBaseBundle:Dashboard:inbox.html.twig', array(

        ));
    }
    
    /**
     * Display User Boats
     * 
     * @return Response
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
     * Add new Boat. Rendering of page will be delegated to Boat bundle.
     * 
     * @return Response
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
     * @param integer $boatId
     * @return Response
     */
    public function boatEditAction($boatId)
    {
        $em = $this->getDoctrine()->getManager();

        $boat = $em->getRepository('ZizooBoatBundle:Boat')->find($boatId);
        if (!$boat) {
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }
        
        // The Punk Ave file uploader part of the Form for Uploading Images
        $editId = $this->getRequest()->get('editId');
        if (!preg_match('/^\d+$/', $editId))
        {
            $editId = sprintf('%09d', mt_rand(0, 1999999999));
            if ($boat->getId())
            {
                $this->get('punk_ave.file_uploader')->syncFiles(
                    array('from_folder' => '../images/boats/' . $boat->getId(),
                        'to_folder' => 'tmp/attachments/' . $editId,
                        'create_to_folder' => true));
            }
        }
        $existingFiles = $this->get('punk_ave.file_uploader')->getFiles(array('folder' => 'tmp/attachments/' . $editId));
        
        $imagesForm = $this->createForm(new ImageType());
        
        return $this->render('ZizooBaseBundle:Dashboard/Boat:edit.html.twig', array(
            'boat'  => $boat,
            'imagesForm'  => $imagesForm->createView(),
            'existingFiles' => $existingFiles,
            'editId' => intval($editId),
            'formAction' => 'ZizooBoatBundle_update'
        ));
    }
    
    /**
     * Adds Images to Existing Boat
     * 
     * @return Response
     */
    public function boatPhotosAction()
    {
        $boatId = $this->getRequest()->get('boatId');
        $em = $this->getDoctrine()->getManager();

        $boat = $em->getRepository('ZizooBoatBundle:Boat')->find($boatId);
        if (!$boat) {
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }
        
        $editId = $this->getRequest()->get('editId');
        if (!preg_match('/^\d+$/', $editId))
        {
            throw new Exception("Bad edit id");
        }
           
        $fileUploader = $this->get('punk_ave.file_uploader');

        /* Get a list of uploaded images to add to Boat */
        $files = $fileUploader->getFiles(array('folder' => '/tmp/attachments/' . $editId));
       
        $images = array();
        foreach ($files as $file) {
            $image = new Image();
            $image->setBoat($boat);
            $image->setPath($file);
            $images[] = $image;
        }

        /* Boat creation is done by Boat Service class */
        $boatService = $this->get('boat_service');
        $boatService->addImages($boat, new ArrayCollection($images));
        
        $fileUploader->syncFiles(
            array('from_folder' => '/tmp/attachments/' . $editId,
            'to_folder' => '../images/boats/' . $boatId,
            'remove_from_folder' => true,
            'create_to_folder' => true)
        );
            
        return $this->redirect($this->generateUrl('ZizooBaseBundle_dashboard_boat_edit', array('id' => $boatId)));
    }
    
    /**
     * Update the Boat Pricing
     * 
     * @return Response
     */
    public function boatPriceAction()
    {
        
        return $this->render('ZizooBaseBundle:Dashboard/Boat:price.html.twig', array(

        ));
    }
    
    /**
     * Display User Skills
     * 
     * @return Response
     */
    public function skillsAction()
    {
        
        return $this->render('ZizooBaseBundle:Dashboard:skills.html.twig', array(

        ));
    }
    
    /**
     * Display User Bookings
     * 
     * @return Response
     */
    public function tripsAction()
    {
        
        return $this->render('ZizooBaseBundle:Dashboard:trips.html.twig', array(
        ));
    }
    
    /**
     * Edit Account Settings
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return Response
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