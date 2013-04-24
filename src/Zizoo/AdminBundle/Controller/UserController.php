<?php

namespace Zizoo\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UserController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction()
    {
        $router     = $this->container->get('router');
        $grid       = $this->container->get('jq_grid_custom');
        
        //OPTIONAL
        $grid->setName('grid_users');
        $grid->setCaption('Users');
        $grid->setOptions(array('height' => 'auto', 
                            'width' => '910',
                            'ondblClickRow' => 'userDoubleClick',
                            'jsonReader' => array(  'repeatitems' => false, 
                                                    'root' => 'rows'
                                            )
                         ));
        $grid->setRouteForced($router->generate('ZizooAdminBundle_User'));
        $grid->setHideIfEmpty(false);

        //MANDATORY
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder()->from('ZizooUserBundle:User', 'user')
                                        ->leftJoin('user.boats', 'boat')
                                        ->leftJoin('user.profile', 'profile')
                                        ->leftJoin('user.bookings', 'booking')
                                        ->select('user.id, user.username, user.email, profile.firstName, profile.lastName, count(boat) as num_boats, count(booking) as num_bookings, user.created, user')
                                        ->groupBy('user.id');
        $grid->setSource($qb);
                
        $extraJS = "";
        
        //COLUMNS DEFINITION
        $grid->addColumn('ID', array('name' => 'id', 'jsonmap' => 'cell.0', 'index' => 'user.id', 'hidden' => false, 'width' => '70', 'sortable' => true, 'search' => true));
        
        $grid->addColumn('Username', array('name' => 'username', 'jsonmap' => 'cell.1', 'index' => 'user.username', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Email', array('name' => 'email', 'jsonmap' => 'cell.2', 'index' => 'user.email', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('First Name', array('name' => 'firstName', 'jsonmap' => 'cell.3', 'index' => 'profile.firstName', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Last Name', array('name' => 'lastName', 'jsonmap' => 'cell.4', 'index' => 'profile.lastName', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Boats', array('name' => 'num_boats', 'jsonmap' => 'cell.5', 'index' => 'num_boats', 'having' => 'count(boat.id)', 'width' => '70', 'sortable' => true, 'search' => true));
        $grid->addColumn('Bookings', array('name' => 'num_bookings', 'jsonmap' => 'cell.6', 'index' => 'num_bookings', 'having' => 'count(booking.id)', 'width' => '70', 'sortable' => true, 'search' => true));
        $grid->addColumn('Created', array('name' => 'created', 'jsonmap' => 'cell.7.date', 'index' => 'user.created', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => true, 'sortable' => true, 'search' => true));
//        $grid->addColumn('Type', array('name' => 'Type', 'jsonmap' => 'cell.2', 'width' => '75', 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
//        $grid->addColumn('Started By', array('name' => 'CreatedBy', 'jsonmap' => 'cell.3', 'width' => '100', 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
//        $grid->addColumn('Start date', array('name' => 'CreatedAt', 'jsonmap' => 'cell.4', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => false, 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
//        $grid->addColumn('Messages', array('name' => 'Messages', 'jsonmap' => 'cell.5', 'width' => '50', 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
//        $grid->addColumn('Last Message', array('name' => 'LastMessage', 'jsonmap' => 'cell.6', 'width' => '150', 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
//        $grid->addColumn('Is Read', array('name' => 'IsRead', 'jsonmap' => 'cell.7', 'hidden' => true, 'sortable' => false, 'search' => false));
//        $grid->addColumn('TypeInt', array('name' => 'TypeInt', 'jsonmap' => 'cell.8', 'hidden' => true, 'sortable' => false, 'search' => false));
        
        
        $grid->setExtraParams(array( 'show_threads'         => (true?'checked="checked"':''),
                                        'loadComplete'      => 'loadComplete',
                                        'extraJS'           => $extraJS));
        
        
       return $grid->render();
        
    }
    
    public function editAction($username)
    {
        $user = $this->getDoctrine()->getManager()->getRepository('ZizooUserBundle:User')->findOneByUsername($username);
        
        if (!$user){
            return $this->redirect($this->generateUrl('ZizooAdminBundle_User'));
        }
        
        return $this->render('ZizooAdminBundle:User:edit.html.twig', array('user'       => $user));
    }
}
