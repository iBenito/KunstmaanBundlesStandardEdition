<?php

namespace Zizoo\AdminBundle\Controller;

use Zizoo\JqGridCustomBundle\Grid\Grid;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;

use Symfony\Component\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ReservationController extends Controller
{
    
    public static function initializeReservationSubGrid(Grid $grid, EntityManager $em, Router $router, $id=null)
    {
        
        //OPTIONAL
        $grid->setGetDataFunction(function($grid){ ReservationController::getReservationSubData($grid); });
        $grid->setName('grid_reservations');
        $grid->setCaption('Reservations');
        $grid->setOptions(array('height' => 'auto', 
                            'width' => '910',
                            'ondblClickRow' => 'reservationDoubleClick',
                            'jsonReader' => array(  'repeatitems' => false, 
                                                    'root' => 'rows'
                                            )
                         ));
        $grid->setRouteForced($router->generate('ZizooAdminBundle_Reservation_sub'));
        $grid->setHideIfEmpty(false);

        //MANDATORY
        $qb = $em->createQueryBuilder()->from('ZizooReservationBundle:Reservation', 'res')
                                        ->leftJoin('res.guest', 'guest')
                                        ->leftJoin('res.boat', 'boat')
                                        ->leftJoin('boat.user', 'owner')
                                        ->leftJoin('res.booking', 'booking')
                                        ->select('res.id, guest.username as guest_username, owner.username as owner_username, boat.name, res.check_in, res.check_out, res.status, res.created, res.updated, booking.id as booking_id, res')
                                        ->groupBy('res.id');
        if ($id){
            $qb = $qb->where('res.id = :res_id')
                        ->setParameter('res_id', $id);
        }
        $grid->setSource($qb);
                
        $extraJS = "";
        
        //COLUMNS DEFINITION
        $grid->addColumn('ID', array('name' => 'id', 'jsonmap' => 'cell.0', 'index' => 'res.id', 'hidden' => false, 'width' => '70', 'sortable' => true, 'search' => true));
        
        $grid->addColumn('Guest', array('name' => 'guest_username', 'jsonmap' => 'cell.1', 'index' => 'guest.username', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Owner', array('name' => 'owner_username', 'jsonmap' => 'cell.2', 'index' => 'owner.username', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Boat', array('name' => 'name', 'jsonmap' => 'cell.3', 'index' => 'boat.name', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Check In', array('name' => 'check_in', 'jsonmap' => 'cell.4.date', 'index' => 'res.check_in', 'width' => '200', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => true, 'sortable' => true, 'search' => true));
        $grid->addColumn('CheckOut', array('name' => 'check_out', 'jsonmap' => 'cell.5.date', 'index' => 'res.check_out', 'width' => '200', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => true, 'sortable' => true, 'search' => true));
        $grid->addColumn('Status', array('name' => 'status', 'jsonmap' => 'cell.6', 'index' => 'res.status', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Created', array('name' => 'created', 'jsonmap' => 'cell.7.date', 'index' => 'res.created', 'width' => '200', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => true, 'sortable' => true, 'search' => true));
        $grid->addColumn('Updated', array('name' => 'updated', 'jsonmap' => 'cell.8.date', 'index' => 'res.updated', 'width' => '200', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => true, 'sortable' => true, 'search' => true));
        $grid->addColumn('Booking ID', array('name' => 'booking_id', 'jsonmap' => 'cell.9', 'index' => 'booking_id', 'hidden' => false, 'width' => '70', 'sortable' => true, 'search' => true));
        $grid->setExtraParams(array( 'show_threads'         => (true?'checked="checked"':''),
                                        'loadComplete'      => 'loadComplete',
                                        'extraJS'           => $extraJS));
        
        return $grid;
    }
    
    /**
     * @Template()
     */
    public function indexAction()
    {
        $router     = $this->container->get('router');
        $grid       = $this->container->get('jq_grid_custom');
        $em         = $this->getDoctrine()->getManager();
        
        //OPTIONAL
        $grid->setGetDataFunction(function($grid){ ReservationController::getReservationData($grid); });
        $grid->setName('grid_reservations');
        $grid->setCaption('Reservations');
        $grid->setOptions(array('height' => 'auto', 
                            'width' => '910',
                            'ondblClickRow' => 'reservationDoubleClick',
                            'jsonReader' => array(  'repeatitems' => false, 
                                                    'root' => 'rows'
                                            )
                         ));
        $grid->setRouteForced($router->generate('ZizooAdminBundle_Reservation'));
        $grid->setHideIfEmpty(false);

        //MANDATORY
        $qb = $em->createQueryBuilder()->from('ZizooReservationBundle:Reservation', 'res')
                                        ->leftJoin('res.guest', 'guest')
                                        ->leftJoin('res.boat', 'boat')
                                        ->leftJoin('boat.user', 'owner')
                                        ->leftJoin('res.booking', 'booking')
                                        ->select('res.id, guest.username as guest_username, owner.username as owner_username, boat.name, res.check_in, res.check_out, res.status, res.created, res.updated, booking.id as booking_id, res')
                                        ->groupBy('res.id');
       
        $grid->setSource($qb);
                
        $extraJS = "";
        
        //COLUMNS DEFINITION
        $grid->addColumn('ID', array('name' => 'id', 'jsonmap' => 'cell.0', 'index' => 'res.id', 'hidden' => false, 'width' => '70', 'sortable' => true, 'search' => true));
        
        $grid->addColumn('Guest', array('name' => 'guest_username', 'jsonmap' => 'cell.1', 'index' => 'guest.username', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Owner', array('name' => 'owner_username', 'jsonmap' => 'cell.2', 'index' => 'owner.username', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Boat', array('name' => 'name', 'jsonmap' => 'cell.3', 'index' => 'boat.name', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Check In', array('name' => 'check_in', 'jsonmap' => 'cell.4.date', 'index' => 'res.check_in', 'width' => '200', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => true, 'sortable' => true, 'search' => true));
        $grid->addColumn('CheckOut', array('name' => 'check_out', 'jsonmap' => 'cell.5.date', 'index' => 'res.check_out', 'width' => '200', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => true, 'sortable' => true, 'search' => true));
        $grid->addColumn('Status', array('name' => 'status', 'jsonmap' => 'cell.6', 'index' => 'res.status', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Created', array('name' => 'created', 'jsonmap' => 'cell.7.date', 'index' => 'res.created', 'width' => '200', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => true, 'sortable' => true, 'search' => true));
        $grid->addColumn('Updated', array('name' => 'updated', 'jsonmap' => 'cell.8.date', 'index' => 'res.updated', 'width' => '200', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => true, 'sortable' => true, 'search' => true));
        $grid->addColumn('Booking ID', array('name' => 'booking_id', 'jsonmap' => 'cell.9', 'index' => 'booking_id', 'hidden' => false, 'width' => '70', 'sortable' => true, 'search' => true));
        $grid->setExtraParams(array( 'show_threads'         => (true?'checked="checked"':''),
                                        'loadComplete'      => 'loadComplete',
                                        'extraJS'           => $extraJS));
        
        
        return $grid->render();
        
    }
    
    /**
     * @Template()
     */
    public function subGridAction()
    {
        $request    = $this->getRequest();
        $router     = $this->container->get('router');
        $grid       = $this->container->get('jq_grid_custom');
        $em         = $this->getDoctrine()->getManager();
        
        $id         = $request->query->get('grid_bookingsreservation_id');
        $grid       = ReservationController::initializeReservationSubGrid($grid, $em, $router, $id);
        
        return $grid->render();
    }
    
    public static function getReservationData(&$grid)
    {
        if ($grid->getSession()->get($grid->getHash()) == 'Y') {

            $request = $grid->getRequest();
            $page = $request->query->get('page', 1);
            $limit = $request->query->get('rows', 1);

            if ($grid->getSourceData()){
                $pagination = $grid->getPaginator()->paginate($grid->getSourceData(), $page, $limit);
            } else {
                $sidx = $request->query->get('sidx');
                $sord = $request->query->get('sord');
                $search = $request->query->get('_search');

                if ($sidx != '') {
                    $grid->getQueryBuilder()->orderBy($sidx, $sord);
                }

                if ($search) {
                    $grid->generateFilters();
                }
                $pagination = $grid->getPaginator()->paginate($grid->getQueryBuilder()->getQuery()->setHydrationMode(Query::HYDRATE_ARRAY), $page, $limit);
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

            $router             = $grid->getContainer()->get('router');
            $trans              = $grid->getContainer()->get('translator');
            $reservationAgent   = $grid->getContainer()->get('zizoo_reservation_reservation_agent');
            
            $columns    = $grid->getColumns();
            $templating = $grid->getTemplating();
            
            foreach ($pagination as $key => $item) {
                $row = $item;

                $val = array();
                foreach ($columns as $c) {
                    $fieldName = $c->getFieldName();
                    $methodName = 'get'.$c->getFieldName();
                    if ($fieldName=='status'){
                        $val[] = $reservationAgent->statusToString($row['status']);
                    } else if ($fieldName=='booking_id'){
                        $val[] = $row['booking_id']!=''?$row['booking_id']:'-';
                    } else if (method_exists($row, $methodName)){
                        $val[] = call_user_func(array( &$row, $methodName)); 
                    } elseif (array_key_exists($c->getFieldName(), $row)) {
                        $val[] = $row[$c->getFieldName()];
                    } elseif ($c->getFieldValue()) {
                        $val[] = $c->getFieldValue();
                    } elseif ($c->getFieldTwig()) {
                        $val[] = $templating
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
    
    public static function getReservationSubData(&$grid)
    {
        if ($grid->getSession()->get($grid->getHash()) == 'Y') {

            $request = $grid->getRequest();
            $page = $request->query->get('page', 1);
            $limit = $request->query->get('rows', 1);

            if ($grid->getSourceData()){
                $pagination = $grid->getPaginator()->paginate($grid->getSourceData(), $page, $limit);
            } else {
                $sidx = $request->query->get('sidx');
                $sord = $request->query->get('sord');
                $search = $request->query->get('_search');

                if ($sidx != '') {
                    $grid->getQueryBuilder()->orderBy($sidx, $sord);
                }

                if ($search) {
                    $grid->generateFilters();
                }
                $pagination = $grid->getPaginator()->paginate($grid->getQueryBuilder()->getQuery()->setHydrationMode(Query::HYDRATE_ARRAY), $page, $limit);
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

            $router             = $grid->getContainer()->get('router');
            $trans              = $grid->getContainer()->get('translator');
            $reservationAgent   = $grid->getContainer()->get('zizoo_reservation_reservation_agent');
            
            $columns    = $grid->getColumns();
            $templating = $grid->getTemplating();
            
            foreach ($pagination as $key => $item) {
                $row = $item;

                $val = array();
                foreach ($columns as $c) {
                    $fieldName = $c->getFieldName();
                    $methodName = 'get'.$c->getFieldName();
                    if ($fieldName=='status'){
                        $val[] = $reservationAgent->statusToString($row['status']);
                    } else if ($fieldName=='booking_id'){
                        $val[] = $row['booking_id']!=''?$row['booking_id']:'-';
                    } else if (method_exists($row, $methodName)){
                        $val[] = call_user_func(array( &$row, $methodName)); 
                    } elseif (array_key_exists($c->getFieldName(), $row)) {
                        $val[] = $row[$c->getFieldName()];
                    } elseif ($c->getFieldValue()) {
                        $val[] = $c->getFieldValue();
                    } elseif ($c->getFieldTwig()) {
                        $val[] = $templating
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
    
    public function editAction($id)
    {
        $reservation = $this->getDoctrine()->getManager()->getRepository('ZizooReservationBundle:Reservation')->findOneById($id);
        
        if (!$reservation){
            return $this->redirect($this->generateUrl('ZizooAdminBundle_Reservation'));
        }
        
        return $this->render('ZizooAdminBundle:Reservation:edit.html.twig', array('reservation'       => $reservation));
    }
}
