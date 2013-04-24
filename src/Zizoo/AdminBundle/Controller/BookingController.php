<?php

namespace Zizoo\AdminBundle\Controller;

use Zizoo\JqGridCustomBundle\Grid\Grid;

use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManager;

use Symfony\Component\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BookingController extends Controller
{
    
    /**
     * @Template()
     */
    public function indexAction()
    {
        $request    = $this->getRequest();
        $router     = $this->container->get('router');
        $grid       = $this->container->get('jq_grid_custom');
        
        //OPTIONAL
        $grid->setGetDataFunction(function($grid){ BookingController::getBookingData($grid); });
        $grid->setName('grid_bookings');
        $grid->setCaption('Bookings');
        $grid->setOptions(array('height' => 'auto', 
                            'width' => '910',
                            'ondblClickRow' => 'bookingDoubleClick',
                            'jsonReader' => array(  'repeatitems' => false, 
                                                    'root' => 'rows'
                                            )
                         ));
        $grid->setRouteForced($router->generate('ZizooAdminBundle_Booking'));
        $grid->setHideIfEmpty(false);

        //MANDATORY
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder()->from('ZizooBookingBundle:Booking', 'booking')
                                        ->leftJoin('booking.reservation', 'res')
                                        ->leftJoin('res.guest', 'guest')
                                        ->leftJoin('res.boat', 'boat')
                                        ->leftJoin('boat.user', 'owner')
                                        ->leftJoin('booking.payment', 'payment')
                                        ->select('booking.id, guest.username as guest_username, owner.username as owner_username, booking.status, booking.created, booking.updated, res.id as reservation_id, booking')
                                        ->groupBy('booking.id');
        $grid->setSource($qb);
                
        $extraJS = "";
        
        //COLUMNS DEFINITION
        $grid->addColumn('ID', array('name' => 'id', 'jsonmap' => 'cell.0', 'index' => 'booking.id', 'hidden' => false, 'width' => '70', 'sortable' => true, 'search' => true));
        
        $grid->addColumn('Guest', array('name' => 'guest_username', 'jsonmap' => 'cell.1', 'index' => 'guest.username', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Owner', array('name' => 'owner_username', 'jsonmap' => 'cell.2', 'index' => 'owner.username', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Status', array('name' => 'status', 'jsonmap' => 'cell.3', 'index' => 'booking.status', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Created', array('name' => 'created', 'jsonmap' => 'cell.4.date', 'index' => 'booking.created', 'width' => '200', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => true, 'sortable' => true, 'search' => true));
        $grid->addColumn('Updated', array('name' => 'updated', 'jsonmap' => 'cell.5.date', 'index' => 'booking.updated', 'width' => '200', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => true, 'sortable' => true, 'search' => true));
        $grid->addColumn('Reservation ID', array('name' => 'reservation_id', 'jsonmap' => 'cell.6', 'index' => 'reservation_id', 'hidden' => false, 'width' => '70', 'sortable' => true, 'search' => true));
        $grid->setExtraParams(array( 'show_threads'         => (true?'checked="checked"':''),
                                        'loadComplete'      => 'loadComplete',
                                        'extraJS'           => $extraJS,
                                        'subGridParams'     => array('grid_bookingsreservation_id')));
        
        
        $id             = $request->query->get('id', null);
        $subGrid        = $this->container->get('jq_grid_custom');
        $subGrid        = ReservationController::initializeReservationSubGrid($subGrid, $em, $router, $id);
        
        $grid->setSubGrid($subGrid);
        
       return $grid->render();
        
    }
    
    public static function getBookingData(&$grid)
    {
        if ($grid->getSession()->get($grid->getHash()) == 'Y') {

            $request = $grid->getRequest();
            $page = $request->query->get('page');
            $limit = $request->query->get('rows');

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
                    if (method_exists($row, $methodName)){
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
