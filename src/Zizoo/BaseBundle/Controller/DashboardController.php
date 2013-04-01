<?php

namespace Zizoo\BaseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Entity\Price;
use Zizoo\BoatBundle\Entity\Image;
use Zizoo\BoatBundle\Form\ImageType;
use Zizoo\BoatBundle\Exception\InvalidPriceException;

use Zizoo\CrewBundle\Form\SkillsType;

use Zizoo\BillingBundle\Form\Model\BankAccount;
use Zizoo\BillingBundle\Form\Type\BankAccountType;
use Zizoo\BillingBundle\Form\Model\PayPal;
use Zizoo\BillingBundle\Form\Type\PayPalType;

use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\ReservationBundle\Exception\InvalidReservationException;

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

        $owner = (count($user->getBoats())) ? true : false;
//        $crew = (count($user->getSkills())) ? true : false;
        
        return $this->render('ZizooBaseBundle::user_widget.html.twig', array(
            'user' => $user,
            'owner' => $owner,
//            'crew' => $crew
        ));
    }
    
    /**
     * Display User Dashboard
     * 
     * @return Response
     */
    public function indexAction()
    {
        $user = $this->getUser();
        
        $reservationsMade = $user->getReservations();
        $bookingsMade = $user->getBookings();
        
        $boats = $user->getBoats();
        
        $reservationRequests = $this->getDoctrine()->getRepository('ZizooReservationBundle:Reservation')->getReservationRequests($user);

        return $this->render('ZizooBaseBundle:Dashboard:index.html.twig', array(
            'reservations' => $reservationsMade,
            'bookings' => $bookingsMade,
            'reservationRequests' => $reservationRequests
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
            'formAction' => 'ZizooBoatBundle_create',
            'formRedirect' => 'ZizooBaseBundle_Dashboard_BoatPhotos'
        ));
    }
    
    /**
     * Edit existing Boat
     * 
     * @param integer $id Boat Id
     * @return Response
     */
    public function boatEditAction($id)
    {
        $user   = $this->getUser();
        $boat   = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat')->find($id);
        if (!$boat || $boat->getUser()!=$user) {
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }
            
        return $this->render('ZizooBaseBundle:Dashboard/Boat:edit.html.twig', array(
            'boat'  => $boat,
            'formAction' => 'ZizooBoatBundle_update',
            'formRedirect' => 'ZizooBaseBundle_Dashboard_BoatEdit'
        ));
    }
    
    /**
     * Add photos to existing Boat
     * 
     * @param integer $id Boat Id
     * @return Response
     */
    public function boatPhotosAction($id)
    {
        $user   = $this->getUser();
        $boat   = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat')->find($id);
        if (!$boat || $boat->getUser()!=$user) {
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
        
        return $this->render('ZizooBaseBundle:Dashboard/Boat:photos.html.twig', array(
            'boat'  => $boat,
            'imagesForm'  => $imagesForm->createView(),
            'existingFiles' => $existingFiles,
            'editId' => intval($editId),
            'formAction' => 'ZizooBoatBundle_update',
            'formRedirect' => 'ZizooBaseBundle_Dashboard_BoatEdit'
        ));
    }
    
    /**
     * Adds Images to Existing Boat
     * 
     * @return Response
     */
    public function boatPhotosCreateAction()
    {
        $user   = $this->getUser();
        $boatId = $this->getRequest()->get('boatId');

        $boat = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat')->find($boatId);
        if (!$boat || $boat->getUser()!=$user) {
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
            
        return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard_BoatEdit', array('id' => $boatId)));
    }
    
    public function confirmBoatPriceAction($id)
    {
        $user               = $this->getUser();
        $session            = $this->container->get('session');
        $em                 = $this->getDoctrine()->getEntityManager();
        
        $boat = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat')->find($id);
        if (!$boat || $boat->getUser()!=$user) {
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }
        
        $overlap        = $session->get('overlap_'.$id);
        if (!$overlap){
            return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard_BoatPrice', array('id' => $id)));
        }
        
        $requestedIds   = $overlap['requested_reservations'];
        $externalIds    = $overlap['external_reservations'];
        
        if (count($requestedIds)==0 && count($externalIds)==0){
            return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard_BoatPrice', array('id' => $id)));
        }
        
        $overlapRequestedReservations = array();
        if (count($requestedIds)>0){
            $overlapRequestedReservations   = $em->getRepository('ZizooReservationBundle:Reservation')->findByIds($requestedIds);
        }
        
        $overlapExternalReservations = array();
        if (count($externalIds)>0){
            $overlapExternalReservations    = $em->getRepository('ZizooReservationBundle:Reservation')->findByIds($externalIds);
        }
        
        return $this->render('ZizooBaseBundle:Dashboard/Boat:price_confirm.html.twig', array(
            'boat'                              => $boat,
            'overlap_requested_reservations'    => $overlapRequestedReservations,
            'overlap_external_reservations'     => $overlapExternalReservations,
            'from'                              => $overlap['from'],
            'to'                                => $overlap['to'],
            'price'                             => $overlap['price'],
            'type'                              => $overlap['type'],
        ));
    }
    
    /**
     * Update the Boat Pricing
     * 
     * @return Response
     */
    public function boatPriceAction($id)
    {
        $request            = $this->getRequest();
        $session            = $this->container->get('session');
        $boatService        = $this->container->get('boat_service');
        $reservationAgent   = $this->container->get('zizoo_reservation_reservation_agent');
        $user               = $this->getUser();
        
        $boat = $this->getDoctrine()->getRepository('ZizooBoatBundle:Boat')->find($id);
        if (!$boat || $boat->getUser()!=$user) {
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }
        
        $reservations   = $boat->getReservation();
        $prices         = $boat->getPrice();
        
        if ($request->isMethod('post')){
            $fromStr    = $request->request->get('date_from', null);
            $toStr      = $request->request->get('date_to', null);
            $p          = $request->request->get('price', null);
            $from       = new \DateTime($fromStr);
            $to         = new \DateTime($toStr);
            $confirmed  = $request->request->get('confirmed', false)=='true';
            
            $type               = $request->request->get('type', 'availability');
            
            $overlapRequestedReservations   = $this->getDoctrine()->getRepository('ZizooReservationBundle:Reservation')->getReservations($user, $boat, $from, $to, array(Reservation::STATUS_REQUESTED));
            $overlapExternalReservations    = $this->getDoctrine()->getRepository('ZizooReservationBundle:Reservation')->getReservations($user, $boat, $from, $to, array(Reservation::STATUS_SELF));
            if (count($overlapRequestedReservations)>0 || count($overlapExternalReservations)>0){

                if ($confirmed){
                    $em                 = $this->getDoctrine()->getEntityManager();

                    foreach ($overlapRequestedReservations as $overlapRequestedReservation){
                        $overlapRequestedReservation->setBoat(null);
                        $boat->removeReservation($overlapRequestedReservation);
                        $em->remove($overlapRequestedReservation);
                    }

                    foreach ($overlapExternalReservations as $overlapExternalReservation){
                        $overlapExternalReservation->setBoat(null);
                        $boat->removeReservation($overlapExternalReservation);
                        $em->remove($overlapExternalReservation);
                    }

                } else {

                    $requestedIds = array();
                    foreach ($overlapRequestedReservations as $overlapRequestedReservation){
                        $requestedIds[] = $overlapRequestedReservation->getId();
                    }

                    $externalIds = array();
                    foreach ($overlapExternalReservations as $overlapExternalReservation){
                        $externalIds[] = $overlapExternalReservation->getId();
                    }
                    
                    $session->set('overlap_'.$id, array('requested_reservations' => $requestedIds, 'external_reservations' => $externalIds, 'from' => $fromStr, 'to' => $toStr, 'price' => $p, 'type' => $type));
                    return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard_ConfirmBoatPrice', array('id' => $id)));
                }
            }
            
            if ($type=='availability' || $type=='default'){
               
                try {
                    $default = $type=='default';
                    $boatService->addPrice($boat, $from, $to, $p, $default, true);
                } catch (InvalidPriceException $e){
                    $this->container->get('session')->getFlashBag()->add('error', $e->getMessage());
                } catch (DBALException $e){
                    $this->container->get('session')->getFlashBag()->add('error', 'Something went wrong');
                }
                return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard_BoatPrice', array('id' => $id)));
            } else if ($type=='unavailability'){
                try {
                    $reservationAgent->makeReservationForSelf($boat, $from, $to, true);
                } catch (InvalidReservationException $e){
                    $this->container->get('session')->getFlashBag()->add('error', $e->getMessage());
                }
                return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard_BoatPrice', array('id' => $id)));
            }
        }
        
        $session->remove('overlap_'.$id);
        
        return $this->render('ZizooBaseBundle:Dashboard/Boat:price.html.twig', array(
            'boat'          => $boat,
            'reservations'  => $reservations,
            'prices'        => $prices,
        ));
    }
        
    /**
     * Display User Skills
     * 
     * @return Response
     */
    public function skillsAction()
    {
        $user = $this->getUser();
        
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
        $user = $this->getUser();
        
        $bookings = $user->getBookings();
        
        return $this->render('ZizooBaseBundle:Dashboard:trips.html.twig', array(
            'bookings' => $bookings
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
        $bankAccountType    = $this->container->get('zizoo_billing.bank_account_type');
        $paypalType         = new PayPalType();
        
        $user               = $this->getUser();
        $braintreeCustomer  = $userService->getPaymentUser($user);

        if ($request->isMethod('POST')){
            if ($request->request->get('zizoo_billing_bank_account', null)){
                if ($braintreeCustomer){
                    $formBraintree = $this->createForm($bankAccountType);
                    $formBraintree->bindRequest($request);

                    if ($formBraintree->isValid()){
                        $bankAccount = $formBraintree->getData();

                        $updateResult = \Braintree_Customer::update(
                            $braintreeCustomer->id,
                            array(
                              'customFields' => array(  'account_owner' => $bankAccount->getAccountOwner(),
                                                        'bank_name'     => $bankAccount->getBankName(),
                                                        'bank_country'  => $bankAccount->getBankCountry(),
                                                        'iban'          => $bankAccount->getIBAN(), 
                                                        'bic'           => $bankAccount->getBIC())
                          )
                        );

                        if ($updateResult->success){
                            $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_billing.bank_account_changed'));
                            return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard_Account'));
                        } else {
                            $this->get('session')->getFlashBag()->add('error', $trans->trans('zizoo_billing.bank_account_not_changed'));
                        }
                    }
                }
            }
            
            if ($request->request->get('zizoo_billing_paypal', null)){
                if ($braintreeCustomer){
                    $formPayPal = $this->createForm($paypalType);
                    $formPayPal->bindRequest($request);
                    
                    if ($formPayPal->isValid()){
                        $paypal = $formPayPal->getData();

                        $updateResult = \Braintree_Customer::update(
                            $braintreeCustomer->id,
                            array(
                              'customFields' => array(  'paypal' => $paypal->getUsername()
                            )
                          )
                        );

                        if ($updateResult->success){
                            $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_billing.paypal_changed'));
                            return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard_Account'));
                        } else {
                            $this->get('session')->getFlashBag()->add('error', $trans->trans('zizoo_billing.paypal_not_changed'));
                        }
                    }
                }
            }
            
        } else {
            if ($braintreeCustomer){
                $bankAccount    = new BankAccount();
                $paypal         = new PayPal();
                if (is_array($braintreeCustomer->customFields)){
                    if (array_key_exists('account_owner', $braintreeCustomer->customFields)){
                        $bankAccount->setAccountOwner($braintreeCustomer->customFields['account_owner']);
                    }
                    if (array_key_exists('bank_name', $braintreeCustomer->customFields)){
                        $bankAccount->setBankName($braintreeCustomer->customFields['bank_name']);
                    }
                    if (array_key_exists('bank_country', $braintreeCustomer->customFields)){
                        $bankAccount->setBankCountry($braintreeCustomer->customFields['bank_country']);
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
                $formBraintree = $this->createForm($bankAccountType, $bankAccount);
                $formPayPal = $this->createForm($paypalType, $paypal);
            }
            
        }
        
        return $this->render('ZizooBaseBundle:Dashboard:settings.html.twig', array(
                    'braintree_form'    => $formBraintree?$formBraintree->createView():null,
                    'braintree_valid'   => $braintreeCustomer!=null,
                    'paypal_form'       => $formPayPal?$formPayPal->createView():null
        ));
    }

}