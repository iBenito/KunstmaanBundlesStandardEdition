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
    
    public function inboxDataAction()
    {
        $user       = $this->getUser();
        $profile    = $user->getProfile();
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $qb = $em->createQueryBuilder()->from('ZizooMessageBundle:Message', 'm')
                                        ->leftJoin('m.recipients', 'r')
                                        ->leftJoin('m.sender_profile', 'sp')
                                        ->leftJoin('sp.user', 'su')
                                        ->select('m.id, m.subject, CONCAT(sp.lastName, CONCAT(\', \', CONCAT(sp.firstName, CONCAT(\' &lt;\', CONCAT(su.email, \'&gt;\')))))) as sender, m.sent')
                                        ->where('r.recipient_profile = :recipient')
                                        ->setParameter('recipient', $profile->getID());
        
        //$qb->expr()->concat('s.firstName', $qb->expr()->concat($qb->expr()->literal(' '), 's.lastName'));
        
        
        
        $response = new Response(json_encode($qb->getQuery()->getArrayResult()));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
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

            $row_id = null;
            $last_row_id = null;
            $last_receiver = null;
            $index = 0;
            foreach ($pagination as $key => $item) {
                $row = $item;
                $row_id = $row['id'];
                if ($row_id==$last_row_id){
                    $row['receiver'] .= ', ' . $last_receiver;
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
                
                if ($row_id==$last_row_id) $response['rows'][$index++]['cell'] = $val;
                
                $last_row_id    = $row_id;
                $last_receiver  = $row['receiver'];
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
