<?php

namespace Zizoo\CharterBundle\Controller;

use Zizoo\CharterBundle\Form\Type\CharterRegistrationType;
use Zizoo\CharterBundle\Entity\CharterRepository;
use Zizoo\CharterBundle\Entity\CharterLogo;

use Zizoo\BillingBundle\Form\Type\PayoutSettingsType;
use Zizoo\BillingBundle\Form\Model\PayoutSettings;
use Zizoo\BillingBundle\Form\Type\BankAccountType;
use Zizoo\BillingBundle\Form\Model\BankAccount;
use Zizoo\BillingBundle\Form\Type\PayPalType;
use Zizoo\BillingBundle\Form\Model\PayPal;

use Zizoo\ReservationBundle\Entity\Reservation;

use Doctrine\ORM\Query;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LanKit\DatatablesBundle\Datatables\Datatable;

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
        $request    = $this->getRequest();
        $session    = $request->getSession();
        $user       = $this->getUser();
        $charter    = $user->getCharter();
        if (!$charter){
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }
        
        $routes = $request->query->get('routes');
        
        $sort               = $request->query->get('sort', 'b.id');
        $dir                = $request->query->get('direction', 'desc');
        $searchBoatName     = $request->query->get('boat_name', null);
        $searchBoatType     = $request->query->get('boat_type', null);
        $page               = $request->attributes->get('page', 1);
        $pageSize           = $request->query->get('page_size', 10);
        $listing_status     = $request->query->get('listing_status');
                
        $em    = $this->getDoctrine()->getManager();
        //$dql   = "SELECT b, c FROM ZizooBoatBundle:Boat b, ZizooCharterBundle:Charter c WHERE ";
        $dql = 'SELECT b, c FROM ZizooBoatBundle:Boat b JOIN b.charter c WHERE c.id = '.$charter->getId()
                .' AND '.($listing_status=='deleted'?'b.deleted IS NOT NULL':'b.deleted IS NULL');
        
        if ($searchBoatName) {
            $dql .= " AND (b.name LIKE '%".$searchBoatName."%' OR b.title LIKE '%".$searchBoatName."%')";
        }
        
        if ($searchBoatType){
            $dql .= " AND b.boatType = '" . $searchBoatType . "'";
        }

        
        switch ($listing_status) {
            case "incomplete":
                $dql .= " AND b.complete = 0";
                break;
            case "hidden":
                $dql .= " AND b.active = 0";
                break;
            case "active":
                $dql .= " AND b.active = 1";
                break;
        }

        if ($sort && $dir){
            $dql .= " ORDER BY " . $sort . " " . $dir;
        }
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        try {
            $pagination = $paginator->paginate(
                $query,
                $page/*page number*/,
                $pageSize/*limit per page*/
            );
            $pagination->setCustomParameters(array('itemName' => 'Boats'));
        } catch (\Exception $e){
            return $this->redirect($this->generateUrl($routes['complete_route']));
        }
        
        $page_sizes = array(
                                        '10'    => '10',
                                        '20'    => '20',
                                        '50'    => '50',
                                        '100'   => '100'
                                    );
        
        $routes = $request->query->get('routes');
        
        $session->remove('step');
        
        $listingStatuses = array(   'all'           => 'All',
                                    'active'        => 'Active',
                                    'incomplete'    => 'Incomplete',
                                    'hidden'        => 'Hidden',
                                    'deleted'       => 'Deleted',);
        
        return $this->render('ZizooCharterBundle:Charter:boats.html.twig', array(
            'pagination'        => $pagination,
            'direction'         => $dir,
            'sort'              => $sort,
            'request_uri'       => $request->getSchemeAndHttpHost().$request->getRequestUri(),
            'search_boat_name'  => $searchBoatName,
            'search_boat_type'  => $searchBoatType,
            'boat_types'        => $em->getRepository('ZizooBoatBundle:BoatType')->findAll(),
            'routes'            => $routes,
            'listing_status'    => $listing_status,
            'listing_statuses'  => $listingStatuses,
            'page_sizes'        => $page_sizes,
            'page'              => $page,
            'page_size'         => $pageSize
        ));
    }
    
    /**
     * Edit Charter Profile
     * 
     * @return Response
     */
    public function profileAction()
    {
        $request    = $this->getRequest();
        $user       = $this->getUser();
        $charter    = $user->getCharter();
        
        if (!$charter) {
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }
        
        $charterType = $this->container->get('zizoo_charter.charter_type');
        $form = $this->createForm($charterType, $charter, array('map_drag'          => true, 
                                                                'map_update'        => true,
                                                                'validation_groups' => array('Default')));
        
        if ($request->isMethod('post')){
            $form->bind($request);
            $charter = $form->getData();
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                
                //setting the updated field manually for file upload DO NOT REMOVE
                $charter->setUpdated(new \DateTime());
                
                $address    = $charter->getAddress();
                $charter->setLogo(null);

                $em->persist($charter);
                $em->persist($address);
                
                $em->flush();
                $this->get('session')->setFlash('notice', 'Your charter profile was updated!');
                return $this->redirect($this->generateUrl($request->query->get('redirect_route')));
            }
            
        }
        
        return $this->render('ZizooCharterBundle:Charter:profile.html.twig',array(
            'form'   => $form->createView()
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
        
        $em                 = $this->getDoctrine()->getManager();
        $userService        = $this->container->get('zizoo_user_user_service');
        $trans              = $this->get('translator');
        $payoutSettingsType = new PayoutSettingsType();
        
        $form = $this->createForm($payoutSettingsType);
        
        $user               = $this->getUser();
        $charter            = $user->getCharter();
        
        if (!$charter) {
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }
        
        $billingUser = $charter->getBillingUser();
        $braintreeCustomer  = $userService->getPaymentUser($billingUser);

        if ($request->isMethod('POST')){
            
            if ($braintreeCustomer){
                $form->bind($request);

                if ($form->isValid()){
                    $payoutSettings = $form->getData();
                    $bankAccount    = $payoutSettings->getBankAccount();
                    $paypal         = $payoutSettings->getPayPal();
                    
                    if ($payoutSettings->getPayoutMethod()->getId()=='bank_transfer'){
                        $updateResult = \Braintree_Customer::update(
                            $braintreeCustomer->id,
                            array(
                              'customFields' => array(  'payout_method' => $payoutSettings->getPayoutMethod()->getId(),
                                                        'account_owner' => $bankAccount->getAccountOwner(),
                                                        'bank_name'     => $bankAccount->getBankName(),
                                                        'bank_country'  => $bankAccount->getCountry()->getIso(),
                                                        'iban'          => $bankAccount->getIBAN(), 
                                                        'bic'           => $bankAccount->getBIC())
                          )
                        );
                    } else if ($payoutSettings->getPayoutMethod()->getId()=='paypal'){
                        $updateResult = \Braintree_Customer::update(
                            $braintreeCustomer->id,
                            array(
                              'customFields' => array(  'payout_method' => $payoutSettings->getPayoutMethod()->getId(),
                                                        'paypal'        => $paypal->getUsername())
                          )
                        );
                    } else {
                        $this->get('session')->getFlashBag()->add('error', $trans->trans('zizoo_billing.payout_settings_not_changed'));
                        return $this->redirect($this->generateUrl($request->query->get('redirect_route')));
                    }

                    if ($updateResult->success){
                        $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_billing.payout_settings_changed'));
                        return $this->redirect($this->generateUrl($request->query->get('redirect_route')));
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
                        $payoutMethod = $em->getRepository('ZizooBillingBundle:PayoutMethod')->findOneById($braintreeCustomer->customFields['payout_method']);
                        $payoutSettings->setPayoutMethod($payoutMethod);
                    }
                    if (array_key_exists('account_owner', $braintreeCustomer->customFields)){
                        $bankAccount->setAccountOwner($braintreeCustomer->customFields['account_owner']);
                    }
                    if (array_key_exists('bank_name', $braintreeCustomer->customFields)){
                        $bankAccount->setBankName($braintreeCustomer->customFields['bank_name']);
                    }
                    if (array_key_exists('bank_country', $braintreeCustomer->customFields)){
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
    
    
    public function usersAction()
    {
        $user       = $this->getUser();
        $charter    = $user->getCharter();
        
        if (!$charter) {
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }        
    }

    public function bookingsJson2Action()
    {
        $datatable = $this->get('lankit_datatables')->getDatatable('ZizooReservationBundle:Reservation');

         // The default type for all joins is inner. Change it to left if desired.
        $datatable->setDefaultJoinType(Datatable::JOIN_LEFT);

        // Can set JOIN_LEFT or JOIN_INNER on a per-column basis
        //$datatable->setJoinType('customer', Datatable::JOIN_INNER);

        // Add the $datatable variable, or other needed variables, to the callback scope
        
        
        return $datatable->getSearchResults();
    }
    
    public function bookingsJsonAction() {
         $user       = $this->getUser();
        $charter    = $user->getCharter();
        
        if (!$charter) {
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }

        //MANDATORY
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder()->from('ZizooReservationBundle:Reservation', 'reservation')
                                        ->leftJoin('reservation.booking', 'booking')
                                        ->leftJoin('reservation.boat', 'boat')
                                        ->leftJoin('boat.charter', 'charter')
                                        ->leftJoin('reservation.guest', 'guest')
                                        ->select('reservation.id as reservation_id, 
                                                    boat.id as boat_id, 
                                                    boat.name as boat_name, 
                                                    reservation.created as created,
                                                    reservation.checkIn as check_in, 
                                                    reservation.checkOut as check_out, 
                                                    guest.id as guest_id,
                                                    guest.username as guest_username,
                                                    reservation.status as reservation_status,
                                                    reservation.hours_to_respond as hours_to_respond,
                                                    reservation
                                                    ')
                                        ->where('charter.id = :charter_id')
                                        ->setParameter('charter_id', $charter->getId())
                                        ->groupBy('reservation.id')
                                        ;
        $cursor = $qb->getQuery()->execute();

        $result = array("aaData" => array());
        foreach ($cursor as $payment) { // queries for all users and data is held internally
            $row = array(
                $payment["reservation_id"],
                $payment["boat_name"],
                $payment["guest_username"],
                'TODO',
                'TODO',
                'TODO',
                $payment["reservation_status"],
                $payment["hours_to_respond"]
                );
            array_push($result["aaData"], $row);
        }

        return new JsonResponse($result);
    }
   
    
    /**
     * @Template()
     */
    public function bookingsAction()
    {
        $user       = $this->getUser();
        $charter    = $user->getCharter();
        $request    = $this->getRequest();
        $em         = $this->getDoctrine()->getManager();
        
        if (!$charter) {
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }
        
        // Get predefined search options
        $reservationOptions = array();
        $boatOptions = array();
        $guestOptions = array();
        $statusOptions = array();
        if (!$request->isXmlHttpRequest()){
            $qb = $em->createQueryBuilder()->from('ZizooReservationBundle:Reservation', 'reservation')
                                            ->leftJoin('reservation.booking', 'booking')
                                            ->leftJoin('reservation.boat', 'boat')
                                            ->leftJoin('boat.charter', 'charter')
                                            ->leftJoin('reservation.guest', 'guest')
                                            ->select('DISTINCT reservation.id as reservation_id')
                                            ->where('charter.id = :charter_id')
                                            ->setParameter('charter_id', $charter->getId());

            $options = $qb->getQuery()->getResult();
            foreach ($options as $option){
                $reservationOptions[$option['reservation_id']] = $option['reservation_id'];
            }
            
            $qb = $em->createQueryBuilder()->from('ZizooBoatBundle:Boat', 'boat')
                                            ->leftJoin('boat.charter', 'charter')
                                            ->select('DISTINCT boat.id as boat_id, boat.name as boat_name')
                                            ->where('charter.id = :charter_id')
                                            ->setParameter('charter_id', $charter->getId());

            $options = $qb->getQuery()->getResult();
            foreach ($options as $option){
                $boatOptions[$option['boat_id']] = $option['boat_name'];
            }
            
            $qb = $em->createQueryBuilder()->from('ZizooReservationBundle:Reservation', 'reservation')
                                            ->leftJoin('reservation.booking', 'booking')
                                            ->leftJoin('reservation.boat', 'boat')
                                            ->leftJoin('boat.charter', 'charter')
                                            ->leftJoin('reservation.guest', 'guest')
                                            ->select('DISTINCT guest.id as guest_id, guest.username as guest_name')
                                            ->where('charter.id = :charter_id')
                                            ->setParameter('charter_id', $charter->getId());

            $options = $qb->getQuery()->getResult();
            foreach ($options as $option){
                $guestOptions[$option['guest_id']] = $option['guest_name'];
            }
            
            $reservationAgent = $this->get('zizoo_reservation_reservation_agent');
            for ($i=0; $i<Reservation::NUM_STATUS; $i++){
                $statusOptions[$i] = $reservationAgent->statusToString($i);
            }
        }
        
        // Define columns
        $columns = array(
            'booking'  => array(
                'title'             => 'Booking',
                'property'          => 'id',
                'bSortable'         => true,
                'search'            => array(
                                                'options' => $reservationOptions
                )
                                               
            ),
            'boat_id' => array(
                'title'             => 'Boat Id',
                'property'          => 'boat.id',
                'bSortable'         => true,
                'search'            => true,
                'visible'           => false
            ),
            'boat_name' => array(
                'title'             => 'Boat',
                'property'          => 'boat.name',
                'bSortable'         => true,
                'search'            => array(
                                                'target'            => 'boat_id',
                                                'options'           => $boatOptions,
                                                'initial_option'    => $request->get('boat', '')
                )
            ),
            'guest_id' => array(
                'title'             => 'Guest ID',
                'property'          => 'guest.id',
                'bSortable'         => true,
                'search'            => true,
                'visible'           => false
            ),
            'guest_name' => array(
                'title'             => 'Guest',
                'property'          => 'guest.username',
                'bSortable'         => true,
                'search'            => array(
                                                'target'            => 'guest_id',
                                                'options'           => $guestOptions,
                                                'initial_option'    => $request->get('guest', '')
                )
            ),
            'created' => array(
                'title'             => 'Created',
                'property'          => 'created',
                'bSortable'         => true,
                'search'            => false,
                'render'            => 'renderDate'
            ),
            'check_in' => array(
                'title'             => 'Check-In',
                'property'          => 'checkIn',
                'bSortable'         => true,
                'search'            => false,
                'render'            => 'renderDate'
            ),
            'check_out' => array(
                'title'             => 'Check-Out',
                'property'          => 'checkOut',
                'bSortable'         => true,
                'search'            => false,
                'render'            => 'renderDate'
            ),
            'status_id' => array(
                'title'             => 'Status ID',
                'property'          => 'status',
                'bSortable'         => false,
                'search'            => true,
                'visible'           => false
            ),
            'status' => array(
                'title'             => 'Status',
                'property'          => 'status',
                'bSortable'         => false,
                'render'            => 'renderStatus',
                'search'            => array(
                                                'target'            => 'status_id',
                                                'options'           => $statusOptions,
                                                'initial_option'    => $request->get('status', '')
                )
            )
        );
        // Create datatable
        $datatable  = $this->get('zizoo_datatables.datatable');
        $datatable->setColumns($columns);
        $datatable->setClass('ZizooReservationBundle:Reservation');
        
        $viewData = $datatable->render();
        if (!$request->isXmlHttpRequest()){
            $viewData['status_options'] = json_encode($statusOptions);
        }
        
        return $viewData;
    }
    
    
    public static function getBookingsData(&$grid)
    {
        if ($grid->getSession()->get($grid->getHash()) == 'Y') {
            
            $request = $grid->getRequest();
            $page = $request->query->get('page');
            $limit = $request->query->get('rows');

            if ($grid->getSourceData()){
                $pagination = $grid->getPaginator()->paginate($grid->getSourceData(), $page, $limit);
            } else {
                $sidx   = $request->query->get('sidx');
                $sord   = $request->query->get('sord');
                $search = $request->query->get('_search');

                if ($sidx != '') {
                    $grid->getQueryBuilder()->orderBy($sidx, $sord);
                }

                if ($search) {
                    $grid->generateFilters();
                }
                $pagination = $grid->getPaginator()->paginate($grid->getQueryBuilder()->getQuery(), $page, $limit);
            }

            $nbRec = $pagination->getTotalItemCount();

            if ($nbRec > 0) {
                $total_pages = ceil($nbRec / $limit);
            } else {
                $total_pages = 0;
            }

            $response = array(
                'page' => $page, 'total' => $total_pages, 'records' => $nbRec
            );

            $reservationAgent   = $grid->getContainer()->get('zizoo_reservation_reservation_agent');
            $router             = $grid->getContainer()->get('router');
            $trans              = $grid->getContainer()->get('translator');
            $columns            = $grid->getColumns();
            $templating         = $grid->getTemplating();
            foreach ($pagination as $key => $item) {
                $row            = $item;
                $reservation    = $item[0];
                $booking        = $reservation->getBooking();
                
                $val = array();
                foreach ($columns as $c) {
                    
                    $fieldName = $c->getFieldName();
                    $methodName = 'get'.$c->getFieldName();
                    if ($fieldName=='reservation_status'){
                        $val[] = $reservationAgent->statusToString($row[$c->getFieldName()]);
                    } else if ($fieldName=='cost'){
                        if ($booking){
                            $cost = $row[$c->getFieldName()];
                            $val[] = number_format($cost, 2).' &euro;';
                        } else {
                            $val[] = '-';
                        }
                    } else if ($fieldName=='payment_total'){
                        if ($booking){
                            $total = $row[$c->getFieldName()];
                            $val[] = ($total?$total:'0.00').' &euro;';
                        } else {
                            $val[] = '-';
                        }
                    } else if ($fieldName=='guest_username'){
                        if ($booking){
                            $val[] = $booking->getRenter()->getUsername();
                        } else {
                            $val[] = '-';
                        }
                    } else if ($fieldName=='hours_to_respond'){
                        $hours = $reservationAgent->hoursToRespond($reservation);
                        $val[] = $hours?$hours:'-';
                    } else if (method_exists($row, $methodName)){
                        $val[] = call_user_func(array( &$row, $methodName)); 
                    } elseif (array_key_exists($c->getFieldName(), $row)) {
                        $val[] = $row[$c->getFieldName()];
                    } elseif ($c->getFieldValue()) {
                        $val[] = $c->getFieldValue();
                    } elseif ($c->getFieldTwig()) {
                        $val[] = $this->templating
                                      ->render($c->getFieldTwig(),
                                        array(
                                            'ligne' => $row
                                        ));
                    } else {
                        $val[] = ' ';
                    }
                    
                    
                }

                $response['rows'][$key]['cell'] = $val;
            }

            $grid->setGetDataFunctionResponse($response);
        } else {
            throw \Exception('Invalid query');
        }
    }


    public function paymentsJsonAction() {
         $user       = $this->getUser();
        $charter    = $user->getCharter();
        
        if (!$charter) {
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }

        //MANDATORY
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder()->from('ZizooBookingBundle:Booking', 'booking')
                                        ->leftJoin('booking.reservation', 'reservation')
                                        ->leftJoin('booking.payment', 'payment')
                                        ->leftJoin('reservation.boat', 'boat')
                                        ->leftJoin('boat.charter', 'charter')
                                        ->leftJoin('reservation.guest', 'guest')
                                        ->select('reservation.id as reservation_id, 
                                                    boat.id as boat_id, 
                                                    boat.name as boat_name, 
                                                    booking.created as created,
                                                    booking.cost as cost, 
                                                    SUM(payment.amount) as payment_total, 
                                                    guest.id as guest_id,
                                                    guest.username as guest_username,
                                                    booking
                                                    ')
                                        ->where('charter.id = :charter_id')
                                        ->setParameter('charter_id', $charter->getId())
                                        ->groupBy('reservation.id')
                                        ;
        $cursor = $qb->getQuery()->execute();

        $result = array("aaData" => array());
        foreach ($cursor as $payment) { // queries for all users and data is held internally
            $row = array(
                $payment["reservation_id"],
                $payment["boat_name"],
                $payment["guest_username"],
                'TODO',
                $payment["cost"],
                $payment["payment_total"]
                );
            array_push($result["aaData"], $row);
        }

        return new JsonResponse($result);
    }
    
    /**
     * @Template()
     */
    public function paymentsAction()
    {
        $user       = $this->getUser();
        $charter    = $user->getCharter();
        
        if (!$charter) {
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }
        
        $router     = $this->container->get('router');
        $grid       = $this->container->get('jq_grid_custom');
        
        //OPTIONAL
        $grid->setGetDataFunction(function($grid){ CharterController::getPaymentsData($grid); });
        $grid->setName('grid_payments');
        $grid->setCaption('Payments');
        $grid->setOptions(array('height' => 'auto', 
                            'width' => '910',
                            'resizeStop'    => 'resizeColumn',
                            'jsonReader' => array(  'repeatitems' => false, 
                                                    'root' => 'rows'
                                            )
                         ));
        
        $grid->setRouteForced($router->generate('ZizooCharterBundle_Charter_Payments'));
        $grid->setHideIfEmpty(false);

        //MANDATORY
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder()->from('ZizooBookingBundle:Booking', 'booking')
                                        ->leftJoin('booking.reservation', 'reservation')
                                        ->leftJoin('booking.payment', 'payment')
                                        ->leftJoin('reservation.boat', 'boat')
                                        ->leftJoin('boat.charter', 'charter')
                                        ->leftJoin('reservation.guest', 'guest')
                                        ->select('reservation.id as reservation_id, 
                                                    boat.id as boat_id, 
                                                    boat.name as boat_name, 
                                                    booking.created as created,
                                                    booking.cost as cost, 
                                                    SUM(payment.amount) as payment_total, 
                                                    guest.id as guest_id,
                                                    guest.username as guest_username,
                                                    booking
                                                    ')
                                        ->where('charter.id = :charter_id')
                                        ->setParameter('charter_id', $charter->getId())
                                        ->groupBy('reservation.id')
                                        ;
        
        
        $grid->setSource($qb);
                
        $extraJS = "";
        
        //COLUMNS DEFINITION
        //public function getReservations(Charter $charter=null, User $user=null, Boat $boat=null, \DateTime $from=null, \DateTime $to=null, $statusArr=null, Reservation $exceptReservation=null)
        $reservations = $em->getRepository('ZizooReservationBundle:Reservation')->getReservations($charter);
        $reservationOptions = array();
        $reservationOptions[''] = 'All';
        $guestOptions = array();
        $guestOptions[''] = 'All';
        foreach ($reservations as $reservation){
            $reservationOptions[$reservation->getId()] = $reservation->getId();
            if (!array_key_exists($reservation->getGuest()->getUsername(), $guestOptions)){
                $guestOptions[$reservation->getGuest()->getUsername()] = $reservation->getGuest()->getUsername();
            }
        }
        $grid->addColumn('Booking', array('name' => 'reservation_id', 'jsonmap' => 'cell.0', 'index' => 'booking.id', 'hidden' => false, 'width' => '70', 'sortable' => true, 'search' => true, 'searchoptions' => array('dataInit' => 'function(elem){ createBookingSearch(elem); }')));
        
        $boats = $em->getRepository('ZizooBoatBundle:Boat')->getCharterBoats($charter);
        $boatOptions = array();
        $boatOptions[''] = 'All';
        foreach ($boats as $boat){
            $boatOptions[$boat->getName()] = $boat->getName();
        }
        $grid->addColumn('Boat Id', array('name' => 'boat_id', 'jsonmap' => 'cell.1', 'index' => 'boat.id', 'hidden' => true, 'width' => '0', 'sortable' => false, 'search' => false));
        $grid->addColumn('Boat', array('name' => 'boat_name', 'jsonmap' => 'cell.2', 'index' => 'boat.name', 'width' => '150', 'sortable' => false, 'search' => false, 'searchoptions' => array('dataInit' => 'function(elem){ createBoatSearch(elem); }')));
        
        $grid->addColumn('Created', array('name' => 'created', 'jsonmap' => 'cell.3.date', 'index' => 'booking.created', 'width' => '100', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y' ), 'datepicker' => true, 'sortable' => true, 'search' => true));

        $grid->addColumn('Total', array('name' => 'cost', 'jsonmap' => 'cell.4', 'index' => 'booking.total', 'hidden' => false, 'width' => '100', 'sortable' => false, 'search' => false));
        $grid->addColumn('Received', array('name' => 'payment_total', 'jsonmap' => 'cell.5', 'index' => 'payment.amount', 'hidden' => false, 'width' => '100', 'sortable' => false, 'search' => false));
        
        $grid->addColumn('Guest Id', array('name' => 'guest_id', 'jsonmap' => 'cell.6', 'index' => 'guest.id', 'hidden' => true, 'width' => '0', 'sortable' => false, 'search' => false));
        $grid->addColumn('Guest', array('name' => 'guest_username', 'jsonmap' => 'cell.7', 'index' => 'guest.username', 'width' => '150', 'sortable' => true, 'search' => true, 'searchoptions' => array('dataInit' => 'function(elem){ createGuestSearch(elem); }')));
        
        $grid->setExtraParams(array(    'bookingOptions'    => $reservationOptions,
                                        'boatOptions'       => $boatOptions,
                                        'guestOptions'      => $guestOptions,
                                        'loadComplete'      => 'loadComplete',
                                        'extraJS'           => $extraJS,
                                        'filterCallback' => 'filterCallback'));
        
        
       return $grid->render();
        
    }
    
    
    public static function getPaymentsData(&$grid)
    {
        if ($grid->getSession()->get($grid->getHash()) == 'Y') {
            
            $request = $grid->getRequest();
            $page = $request->query->get('page');
            $limit = $request->query->get('rows');

            if ($grid->getSourceData()){
                $pagination = $grid->getPaginator()->paginate($grid->getSourceData(), $page, $limit);
            } else {
                $sidx   = $request->query->get('sidx');
                $sord   = $request->query->get('sord');
                $search = $request->query->get('_search');

                if ($sidx != '') {
                    $grid->getQueryBuilder()->orderBy($sidx, $sord);
                }

                if ($search) {
                    $grid->generateFilters();
                }
                $pagination = $grid->getPaginator()->paginate($grid->getQueryBuilder()->getQuery(), $page, $limit);
            }

            $nbRec = $pagination->getTotalItemCount();

            if ($nbRec > 0) {
                $total_pages = ceil($nbRec / $limit);
            } else {
                $total_pages = 0;
            }

            $response = array(
                'page' => $page, 'total' => $total_pages, 'records' => $nbRec
            );

            $reservationAgent   = $grid->getContainer()->get('zizoo_reservation_reservation_agent');
            $router             = $grid->getContainer()->get('router');
            $trans              = $grid->getContainer()->get('translator');
            $columns            = $grid->getColumns();
            $templating         = $grid->getTemplating();
            foreach ($pagination as $key => $item) {
                $row            = $item;
                $booking        = $item[0];
                
                $val = array();
                foreach ($columns as $c) {
                    
                    $fieldName = $c->getFieldName();
                    $methodName = 'get'.$c->getFieldName();
                    if ($fieldName=='cost'){
                        if ($booking){
                            $cost = $row[$c->getFieldName()];
                            $val[] = number_format($cost, 2).' &euro;';
                        } else {
                            $val[] = '-';
                        }
                    } else if ($fieldName=='payment_total'){
                        if ($booking){
                            $total = $row[$c->getFieldName()];
                            $val[] = number_format(($total?$total:0), 2).' &euro;';
                        } else {
                            $val[] = '-';
                        }
                    } else if ($fieldName=='guest_username'){
                        if ($booking){
                            $val[] = $booking->getRenter()->getUsername();
                        } else {
                            $val[] = '-';
                        }
                    } else if (method_exists($row, $methodName)){
                        $val[] = call_user_func(array( &$row, $methodName)); 
                    } elseif (array_key_exists($c->getFieldName(), $row)) {
                        $val[] = $row[$c->getFieldName()];
                    } elseif ($c->getFieldValue()) {
                        $val[] = $c->getFieldValue();
                    } elseif ($c->getFieldTwig()) {
                        $val[] = $this->templating
                                      ->render($c->getFieldTwig(),
                                        array(
                                            'ligne' => $row
                                        ));
                    } else {
                        $val[] = ' ';
                    }
                    
                    
                }

                $response['rows'][$key]['cell'] = $val;
            }

            $grid->setGetDataFunctionResponse($response);
        } else {
            throw \Exception('Invalid query');
        }
    }
    
    public function viewBookingAction($id)
    {
        $em         = $this->getDoctrine()->getManager();
        $user       = $this->getUser();
        $charter    = $user->getCharter();
        
        if (!$charter){
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }
        
        $reservation   = $em->getRepository('ZizooReservationBundle:Reservation')->find($id);
        if (!$reservation){
            return $this->redirect($this->generateUrl('ZizooCharterBundle_Charter_Bookings'));
        }
        
        $boat   = $reservation->getBoat();
        //if ($boat->getCharter()->getAdminUser()!=$user) {
        if (!$boat || !$boat->getCharter()->getUsers()->contains($user)){
            throw $this->createNotFoundException('Unable to find Boat entity.');
        }
        
        return $this->render('ZizooCharterBundle:Charter:view_reservation.html.twig', array(
            'reservation'       => $reservation
        ));
    }
    
    public function setLogoAction()
    {
        $request    = $this->getRequest();
        $user       = $this->getUser();
        $charter    = $user->getCharter();
            
        $imageFile      = $request->files->get('logoFile');
        $charterService = $this->container->get('zizoo_charter_charter_service');

        try {
            $logo = $charterService->setCharterLogo($charter, $imageFile, true);
            return new JsonResponse(array('message' => 'Your logo has been uploaded successfully', 'id' => $logo->getId()));
        } catch (\Exception $e){
            return new Response($e->getMessage(), 400);
        }
    }
    
    public function getLogoAction()
    {
        $request    = $this->getRequest();
        $user       = $this->getUser();
        $profile    = $user->getProfile();
                
        $request    = $this->getRequest();
        $user       = $this->getUser();
        $charter    = $user->getCharter();
        
        if (!$charter) {
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }
        
        
        $charterType = $this->container->get('zizoo_charter.charter_type');
        $form = $this->createForm($charterType, $charter, array('map_drag'          => true, 
                                                                'map_update'        => true,
                                                                'validation_groups' => array('Default')));
        
        return $this->render('ZizooCharterBundle:Charter:logo.html.twig',array(
            'form'     => $form->createView()
        ));
    }
    
}