<?php

namespace Zizoo\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BoatController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction()
    {
        $router     = $this->container->get('router');
        $grid       = $this->container->get('jq_grid_custom');
        
        //OPTIONAL
        $grid->setName('grid_boats');
        $grid->setCaption('Boats');
        $grid->setOptions(array('height' => 'auto', 
                            'width' => '910',
                            'ondblClickRow' => 'boatDoubleClick',
                            'jsonReader' => array(  'repeatitems' => false, 
                                                    'root' => 'rows'
                                            )
                         ));
        $grid->setRouteForced($router->generate('ZizooAdminBundle_Boat'));
        $grid->setHideIfEmpty(false);

        //MANDATORY
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder()->from('ZizooBoatBundle:Boat', 'boat')
                                        ->leftJoin('boat.charter', 'charter')
                                        ->select('boat.id, boat.name, boat.title, charter.charterName, boat.created, boat')
                                        ->groupBy('boat.id');

        $grid->setSource($qb);
                
        $extraJS = "";
        
        //COLUMNS DEFINITION
        $grid->addColumn('ID', array('name' => 'id', 'jsonmap' => 'cell.0', 'index' => 'boat.id', 'hidden' => false, 'width' => '70', 'sortable' => true, 'search' => true));
        
        $grid->addColumn('Name', array('name' => 'name', 'jsonmap' => 'cell.1', 'index' => 'boat.name', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Title', array('name' => 'title', 'jsonmap' => 'cell.2', 'index' => 'boat.title', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Owner', array('name' => 'charterName', 'jsonmap' => 'cell.3', 'index' => 'charter.charterName', 'width' => '200', 'sortable' => true, 'search' => true));
        $grid->addColumn('Created', array('name' => 'created', 'jsonmap' => 'cell.4.date', 'index' => 'boat.created', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => true, 'sortable' => true, 'search' => true));

        
        $grid->setExtraParams(array(    'loadComplete'      => 'loadComplete',
                                        'extraJS'           => $extraJS));
        
        
       return $grid->render();
        
    }
    
    public function editAction($id)
    {
        $boat = $this->getDoctrine()->getManager()->getRepository('ZizooBoatBundle:Boat')->findOneById($id);
        
        if (!$boat){
            return $this->redirect($this->generateUrl('ZizooAdminBundle_Boat'));
        }
        
        return $this->render('ZizooAdminBundle:Boat:edit.html.twig', array('boat'       => $boat));
    }
}
