<?php

namespace Zizoo\MessageBundle\Controller;

use Zizoo\MessageBundle\Extensions\DoctrineExtensions\Query\Mysql\GroupConcat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Zizoo\JqGridCustomBundle\Grid\Grid;

class MessageController extends Controller
{
    /**
     * @Template()
     */
    public function inboxAction()
    {
        $user       = $this->getUser();
        $profile    = $user->getProfile();
        
        $em = $this->getDoctrine()->getEntityManager();

        $qb = $em->getRepository('ZizooMessageBundle:Message')->getInboxMessagesQueryBuilder($profile);
        
        $grid = $this->get('jq_grid_custom');
        
        //OPTIONAL
        $grid->setName('grid_inbox');
        $grid->setCaption('Inbox');
        $grid->setOptions(array('height' => 'auto', 
                            'width' => '910',
                            'ondblClickRow' => 'messageDoubleClick',
                            'jsonReader' => array(  'repeatitems' => false, 
                                                    'root' => 'rows'
                                            )
                         ));
        $grid->setRouteForced($this->get('router')->generate('inbox'));
        $grid->setHideIfEmpty(false);

        //MANDATORY
        $grid->setSource($qb);

        //COLUMNS DEFINITION
        $grid->addColumn('ID', array('name' => 'id', 'jsonmap' => 'cell.0','index' => 'm.id', 'hidden' => true, 'sortable' => false, 'search' => false));
        $grid->addColumn('Subject', array('name' => 'subject', 'jsonmap' => 'cell.1', 'index' => 'm.subject', 'width' => '250', 'search' => true));
        $grid->addColumn('From', array('name' => 'sender', 'jsonmap' => 'cell.2', 'index' => 'sp.lastName, sp.firstName, su.email', 'width' => '150', 'search' => false));
        $grid->addColumn('Received', array('name' => 'sent', 'jsonmap' => 'cell.3.date', 'index' => 'm.sent', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => true));
               
        return ($grid->render());
    }
    
    
    static function sentGetData(&$grid){
        if ($grid->getSession()->get($grid->getHash()) == 'Y') {

            $page = $grid->getRequest()->query->get('page');
            $limit = $grid->getRequest()->query->get('rows');
            $sidx = $grid->getRequest()->query->get('sidx');
            $sord = $grid->getRequest()->query->get('sord');
            $search = $grid->getRequest()->query->get('_search');

            if ($sidx != '') {
                $grid->qb->orderBy($sidx, $sord);
            }

            if ($search) {
                $grid->generateFilters();
            }

            $pagination = $grid->getPaginator()->paginate($grid->getSource()->getQuery()->setHydrationMode($grid->getHydrationMode()), $page, $limit);

            $nbRec = $pagination->getTotalItemCount();

            if ($nbRec > 0) {
                $total_pages = ceil($nbRec / $limit);
            } else {
                $total_pages = 0;
            }

            $response = array(
                'page' => $page, 'total' => $total_pages, 'records' => $nbRec
            );

            $message_id         = null;
            $last_message_id    = null;
            $last_receiver      = null;
            $last_val           = null;
            $index = 0;
            $all_receivers = null;
            $output = false;
            foreach ($pagination as $key => $item) {
                $row = $item;
                $message_id = $row['message_id'];
                if ($message_id==$last_message_id){
                    $all_receivers = $row['receiver'] . ', ' . $last_receiver;
                } else if ($last_val){
                    $last_val[2] = $all_receivers;
                } else {
                    $all_receivers = $row['receiver'];
                }
                $val = array();
                $columns = $grid->getColumns();
                $templating = $grid->getTemplating();
                foreach ($columns as $c) {
                    if (array_key_exists($c->getFieldName(), $row)) {
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
                
                if ($message_id!=$last_message_id && $last_val) {
                    $response['rows'][$index++]['cell'] = $last_val;
                    $output = true;
                } else {
                    $output = false;
                }
                
                $last_message_id    = $message_id;
                $last_receiver      = $row['receiver'];
                $last_val           = $val;
            }
            if (!$output){
                $last_val[2] = $all_receivers;
                $response['rows'][$index++]['cell'] = $last_val;
            }
            $grid->setGetDataFunctionResponse($response);
        } else {
            throw \Exception('Invalid query');
        }
    }
    
    /**
     * @Template()
     */
    public function sentAction()
    {
        $user       = $this->getUser();
        $profile    = $user->getProfile();
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $qb = $em->getRepository('ZizooMessageBundle:Message')->getSentQueryBuilder($profile);

        $grid = $this->get('jq_grid_custom');
        
        //OPTIONAL
        $grid->setGetDataFunction(function($grid){ MessageController::sentGetData($grid); });
        $grid->setName('grid_sent');
        $grid->setCaption('Sent');
        $grid->setOptions(array('height' => 'auto', 
                            'width' => '910',
                            'ondblClickRow' => 'messageDoubleClick',
                            'jsonReader' => array(  'repeatitems' => false, 
                                                    'root' => 'rows'
                                            )
                         ));
        $grid->setRouteForced($this->get('router')->generate('sent'));
        $grid->setHideIfEmpty(false);

        //MANDATORY
        $grid->setSource($qb);

        //COLUMNS DEFINITION
        $grid->addColumn('ID', array('name' => 'id', 'jsonmap' => 'cell.0','index' => 'm.id', 'hidden' => true, 'sortable' => false, 'search' => false));
        $grid->addColumn('Subject', array('name' => 'subject', 'jsonmap' => 'cell.1', 'index' => 'm.subject', 'width' => '250', 'search' => true));
        $grid->addColumn('To', array('name' => 'receiver', 'jsonmap' => 'cell.2', 'index' => 'sp.lastName, sp.firstName, su.email', 'width' => '150', 'search' => false));
        $grid->addColumn('Received', array('name' => 'sent', 'jsonmap' => 'cell.3.date', 'index' => 'm.sent', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => true));
               
        
        return ($grid->render());
    }
}
