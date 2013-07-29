<?php

namespace Zizoo\MessageBundle\Controller;

use Zizoo\MessageBundle\Entity\Thread;
use Zizoo\MessageBundle\Form\Model\NewThreadMultipleMessage;

use FOS\MessageBundle\Controller\MessageController as BaseController;
use FOS\MessageBundle\Provider\ProviderInterface;
use FOS\MessageBundle\Model\ParticipantInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MessageController extends BaseController
{
    /**
     * @Template()
     */
//    public function inboxAction()
//    {
//        $request    = $this->container->get('request');
//        
//        $threads    = $this->getProvider()->getInboxThreads();
//        $router     = $this->container->get('router');
//        $grid       = $this->container->get('jq_grid_custom');
//        
//        //OPTIONAL
//        $grid->setGetDataFunction(function($grid){ MessageController::getInboxData($grid); });
//        $grid->setName('grid_inbox');
//        $grid->setCaption('Inbox');
//        $grid->setOptions(array('height' => 'auto', 
//                            'width' => '910',
//                            'ondblClickRow' => 'messageDoubleClick',
//                            'jsonReader' => array(  'repeatitems' => false, 
//                                                    'root' => 'rows'
//                                            )
//                         ));
//        $grid->setRouteForced($router->generate('fos_message_inbox', array('show_threads' => true)));
//        $grid->setHideIfEmpty(false);
//
//        //MANDATORY
//        $grid->setSourceData($threads);
//        
//        
//        $formatFnc = "function(cellValue, options, rowObject){"
//                                ."var cellClass = 'unread';"
//                                ."if (rowObject.cell[7]) cellClass='read';"
//                                ."return '<div class=\"'+cellClass+'\">'+cellValue+'</div>'"
//                                ."}";
//        
//        $extraJS = "function openThread(threadId, type){"
//                    ."  switch (type){"
//                    ."      case '0':"
//                    ."          url = '".$router->generate('ZizooMessageBundle_thread_view')."/'+threadId;"
//                    ."          viewThread(url);"
//                    ."          break;"
//                    ."      case '1':"
//                    ."          alert('Feature not available yet');"
//                    ."          break;"
//                    ."      case '2':"
//                    ."          alert('Feature not available yet');"
//                    ."          break;"
//                    ."      case '3':"
//                    ."          alert('Feature not available yet');"
//                    ."          break;"
//                    ."      case '4':"
//                    ."          alert('Feature not available yet');"
//                    ."          break;"
//                    ."      default:"
//                    ."          url = '".$router->generate('ZizooMessageBundle_thread_view')."/'+threadId;"
//                    ."          viewThread(url);"
//                    ."          break;"
//                    ."  }"
//                    ."}";
//        
//        $messageId = $request->query->get('messageId', null);
//        if ($messageId){
//            $em = $this->container->get('doctrine.orm.entity_manager');
//            $message = $em->getRepository('ZizooMessageBundle:Message')->findOneById($messageId);
//            if ($message){
//                $thread = $message->getThread();
//                $extraJS    .= "\n\n"
//                            ."$(document).ready(function(){"
//                            ."  url = '".$router->generate('ZizooMessageBundle_thread_view')."/".$thread->getID()."';"
//                            ."  viewThread(url);"
//                            ."});";
//            }
//        }
//        
//        //COLUMNS DEFINITION
//        $grid->addColumn('ID', array('name' => 'Id', 'jsonmap' => 'cell.0','index' => 'm.id', 'hidden' => true, 'sortable' => false, 'search' => false));
//        
//        $grid->addColumn('Subject', array('name' => 'Subject', 'jsonmap' => 'cell.1', 'width' => '200', 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
//        $grid->addColumn('Type', array('name' => 'Type', 'jsonmap' => 'cell.2', 'width' => '75', 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
//        $grid->addColumn('Started By', array('name' => 'CreatedBy', 'jsonmap' => 'cell.3', 'width' => '100', 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
//        $grid->addColumn('Start date', array('name' => 'CreatedAt', 'jsonmap' => 'cell.4', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => false, 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
//        $grid->addColumn('Messages', array('name' => 'Messages', 'jsonmap' => 'cell.5', 'width' => '50', 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
//        $grid->addColumn('Last Message', array('name' => 'LastMessage', 'jsonmap' => 'cell.6', 'width' => '150', 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
//        $grid->addColumn('Is Read', array('name' => 'IsRead', 'jsonmap' => 'cell.7', 'hidden' => true, 'sortable' => false, 'search' => false));
//        $grid->addColumn('TypeInt', array('name' => 'TypeInt', 'jsonmap' => 'cell.8', 'hidden' => true, 'sortable' => false, 'search' => false));
//        
//        $inboxUrl = $request->query->get('inbox_url');
//        $sentUrl  = $request->query->get('sent_url');
//        
////        $user = $this->container->get('security.context')->getToken()->getUser();
////        if ($user->getCharter()!=null){
////            $inboxUrl   = 'ZizooBaseBundle_Dashboard_CharterInbox';
////            $sentUrl    = 'ZizooBaseBundle_Dashboard_CharterSent';
////        } else {
////            $inboxUrl   = 'ZizooBaseBundle_Dashboard_Inbox';
////            $sentUrl    = 'ZizooBaseBundle_Dashboard_Sent';
////        }
//        
//        $grid->setExtraParams(array( 'show_threads'         => (true?'checked="checked"':''),
//                                        'loadComplete'      => 'loadComplete',
//                                        'url_threads'       => $this->container->get('router')->generate('fos_message_inbox', array('show_threads' => true)),
//                                        'url_no_threads'    => $this->container->get('router')->generate('fos_message_inbox', array('show_threads' => false)),
//                                        'extraJS'           => $extraJS,
//                                        'inbox_url'         => $inboxUrl,
//                                        'sent_url'          => $sentUrl));
//        
//        
//        return $grid->render();
//    }
    
    public function inboxAction()
    {
        $request    = $this->container->get('request');
        $user       = $this->container->get('security.context')->getToken()->getUser();
        
        $routes = $request->query->get('routes');
        
        $qb = $this->getParticipantThreadsQueryBuilder($user);
        
        $page     = $request->attributes->get('page', 1);
        $pageSize = $request->query->get('page_size', 10);
        
        $paginator  = $this->container->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $page/*page number*/,
            $pageSize/*limit per page*/
        );
        $pagination->setCustomParameters(array('itemName' => 'Threads'));
        
        return $this->container->get('templating')->renderResponse('ZizooMessageBundle:Message:inbox2.html.twig', array(
            'pagination'        => $pagination,
            'user'              => $user,
            'routes'            => $routes
        ));
    }
    
    public static function getInboxData(&$grid)
    {
        if ($grid->getSession()->get($grid->getHash()) == 'Y') {
            
            $request = $grid->getRequest();
            $page = $request->query->get('page');
            $limit = $request->query->get('rows');

            /**
            if ($search) {
                $this->generateFilters();
            }*/

            $pagination = $grid->getPaginator()->paginate($grid->getSourceData(), $page, $limit);

            $nbRec = $pagination->getTotalItemCount();

            if ($nbRec > 0) {
                $total_pages = ceil($nbRec / $limit);
            } else {
                $total_pages = 0;
            }

            $response = array(
                'page' => $page, 'total' => $total_pages, 'records' => $nbRec
            );

            $router = $grid->getContainer()->get('router');
            $trans  = $grid->getContainer()->get('translator');
            $columns = $grid->getColumns();
            $templating = $grid->getTemplating();
            foreach ($pagination as $key => $item) {
                $row = $item;

                $val = array();
                foreach ($columns as $c) {
                    if ($row instanceof Thread){
                        $fieldName = $c->getFieldName();
                        $methodName = 'get'.$c->getFieldName();
                        if ($fieldName=='CreatedBy'){
                            $val[] = $row->getCreatedBy()->getUsername();
                        } else if ($fieldName=='Messages'){
                            $val[] = count($row->getMessages());
                        } else if ($fieldName=='LastMessage'){
                            $lastMessage = $row->getLastMessage();
                            if ($lastMessage){
                                $title = $trans->trans('goto_last', array(), 'FOSMessageBundle');'';//{% trans from 'FOSMessageBundle' %}goto_last{% endtrans %}
                                $link = $router->generate('fos_message_thread_view', array('threadId' => $row->getId()));
                                $line = '<a href="'.$link.'#message_'.$lastMessage->getId().'" title="'.$title.'">→</a>';                                
                                $date = $trans->trans('on', array('%date%' => $lastMessage->getCreatedAt()->format('d/m/Y H:i')), 'FOSMessageBundle');//{% trans with {'%date%': thread.lastMessage.createdAt|date} from 'FOSMessageBundle' %}on{% endtrans %}
                                $by   = $trans->trans('by', array('%sender%' => $lastMessage->getSender()->getUsername()), 'FOSMessageBundle');//{% trans with {'%sender%': thread.lastMessage.sender|e } from 'FOSMessageBundle' %}by{% endtrans %}
                                $line .= $date.'<br />';
                                $line .= $by;
                                $val[] = $line;
                            } else {
                                $val[] = '----';
                            }
                        } else if ($fieldName=='CreatedAt'){
                            $val[] = $row->getCreatedAt()->format('d/m/Y H:i');
                        } else if ($fieldName=='IsRead'){
                            $user = $grid->getContainer()->get('security.context')->getToken()->getUser();
                            $val[] = $row->isReadByParticipant($user);
                        } else if ($fieldName=='Type'){
                            //$val[] = $row->getThreadType()->getName();
                            //$lastMessageType = $row->getLastMessage()->getMessageType();
                            $lastMessageType = $row->getLastMessageType();
                            $val [] = $lastMessageType?$lastMessageType->getName():'General';
                        } else if ($fieldName=='TypeInt'){
                            //$val[] = $row->getThreadType()->getId();
                            //$lastMessageType = $row->getLastMessage()->getMessageType();
                            $lastMessageType = $row->getLastMessageType();
                            $val [] = $val [] = $lastMessageType?$lastMessageType->getId():'';
                        } else if (method_exists($row, $methodName)){
                            $val[] = call_user_func(array( &$row, $methodName)); 
                        } else {
                            $val[] = ' ';
                        }
                    }
                    
                }

                $response['rows'][$key]['cell'] = $val;
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
        $request    = $this->container->get('request');
        
        $threads    = $this->getProvider()->getSentThreads();
        $router     = $this->container->get('router');
        $grid       = $this->container->get('jq_grid_custom');
        
        //OPTIONAL
        $grid->setGetDataFunction(function($grid){ MessageController::getInboxData($grid); });
        $grid->setName('grid_sent');
        $grid->setCaption('Sent');
        $grid->setOptions(array('height' => 'auto', 
                            'width' => '910',
                            'ondblClickRow' => 'messageDoubleClick',
                            'jsonReader' => array(  'repeatitems' => false, 
                                                    'root' => 'rows'
                                            )
                         ));
        $grid->setRouteForced($router->generate('fos_message_sent', array('show_threads' => true)));
        $grid->setHideIfEmpty(false);

        //MANDATORY
        $grid->setSourceData($threads);
        
        
        $formatFnc = "function(cellValue, options, rowObject){"
                                ."var cellClass = 'unread';"
                                ."if (rowObject.cell[7]) cellClass='read';"
                                ."return '<div class=\"'+cellClass+'\">'+cellValue+'</div>'"
                                ."}";
        
        $extraJS = "function openThread(threadId, type){"
                    ."  switch (type){"
                    ."      case '0':"
                    ."          url = '".$router->generate('ZizooMessageBundle_thread_view')."/'+threadId;"
                    ."          viewThread(url);"
                    ."          break;"
                    ."      case '1':"
                    ."          alert('Feature not available yet');"
                    ."          break;"
                    ."      case '2':"
                    ."          alert('Feature not available yet');"
                    ."          break;"
                    ."      case '3':"
                    ."          alert('Feature not available yet');"
                    ."          break;"
                    ."      case '4':"
                    ."          alert('Feature not available yet');"
                    ."          break;"
                    ."      default:"
                    ."          url = '".$router->generate('ZizooMessageBundle_thread_view')."/'+threadId;"
                    ."          viewThread(url);"
                    ."          break;"
                    ."  }"
                    ."}";
        
        //COLUMNS DEFINITION
        $grid->addColumn('ID', array('name' => 'Id', 'jsonmap' => 'cell.0','index' => 'm.id', 'hidden' => true, 'sortable' => false, 'search' => false));
        
        $grid->addColumn('Subject', array('name' => 'Subject', 'jsonmap' => 'cell.1', 'width' => '200', 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
        $grid->addColumn('Type', array('name' => 'Type', 'jsonmap' => 'cell.2', 'width' => '75', 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
        $grid->addColumn('Started By', array('name' => 'CreatedBy', 'jsonmap' => 'cell.3', 'width' => '100', 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
        $grid->addColumn('Start date', array('name' => 'CreatedAt', 'jsonmap' => 'cell.4', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => false, 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
        $grid->addColumn('Messages', array('name' => 'Messages', 'jsonmap' => 'cell.5', 'width' => '50', 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
        $grid->addColumn('Last Message', array('name' => 'LastMessage', 'jsonmap' => 'cell.6', 'width' => '150', 'sortable' => false, 'search' => false, 'formatter' => $formatFnc));
        $grid->addColumn('Is Read', array('name' => 'IsRead', 'jsonmap' => 'cell.7', 'hidden' => true, 'sortable' => false, 'search' => false));
        $grid->addColumn('TypeInt', array('name' => 'TypeInt', 'jsonmap' => 'cell.8', 'hidden' => true, 'sortable' => false, 'search' => false));
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user->getCharter()!=null){
            $inboxUrl   = 'ZizooBaseBundle_Dashboard_CharterInbox';
            $sentUrl    = 'ZizooBaseBundle_Dashboard_CharterSent';
        } else {
            $inboxUrl   = 'ZizooBaseBundle_Dashboard_Inbox';
            $sentUrl    = 'ZizooBaseBundle_Dashboard_Sent';
        }
        
        $grid->setExtraParams(array( 'show_threads'         => (true?'checked="checked"':''),
                                        'loadComplete'      => 'loadComplete',
                                        'url_threads'       => $this->container->get('router')->generate('fos_message_sent', array('show_threads' => true)),
                                        'url_no_threads'    => $this->container->get('router')->generate('fos_message_sent', array('show_threads' => false)),
                                        'extraJS'           => $extraJS,
                                        'inbox_url'         => $inboxUrl,
                                        'sent_url'          => $sentUrl));
        
        
        return $grid->render();
    }

    /**
     * Displays a thread, also allows to reply to it
     *
     * @param strind $threadId the thread id
     * @return Response
     */
    public function threadAction($threadId, $ajax=false)
    {
        if (!$threadId){
            return new RedirectResponse($this->container->get('router')->generate('fos_message_inbox'));
        }
        $request                = $this->container->get('request');
        if (!$ajax) $ajax       = $request->isXmlHttpRequest();
        
        $routes         = $request->query->get('routes');
        
        $user           = $this->container->get('security.context')->getToken()->getUser();
        $charter        = $user->getCharter();
        
        $provider       = $this->getProvider();
        $thread         = $provider->getThread($threadId);
        $form           = $this->container->get('zizoo_message.reply_form.factory')->create($thread);
        $formHandler    = $this->container->get('zizoo_message.reply_form.handler');
        
        $messageTypeRepo    = $this->container->get('doctrine.orm.entity_manager')->getRepository('ZizooMessageBundle:MessageType');
        $messageTypes       = $messageTypeRepo->findAll();
        
        $messageTypesArr = array();
        foreach ($messageTypes as $messageType){
            $messageTypesArr[$messageType->getId()] = array('id' => $messageType->getId(), 'name' => $messageType->getName());
        }
        
        if ($message = $formHandler->process($form)) {
            if ($ajax){
                return new Response();
            } else {
                return new RedirectResponse($this->container->get('router')->generate($routes['view_thread_route'], array(
                    'threadId'      => $message->getThread()->getId(),
                    'message_types' => $messageTypesArr,
                    'user'          => $user,
                    'ajax'          => $ajax
                )));
            }
        }

        $headers = array();
        $participantsArr = array();
        $participants = $thread->getParticipants();
        foreach ($participants as $participant){
            if ($participant->getId() == $user->getId()) continue;
            $participantsArr[] = $participant->getProfile()->getFirstName();
        }
        $headers['x-zizoo-thread-subject'] = $thread->getSubject();
        $headers['x-zizoo-thread-participants'] = implode(',', $participantsArr);
        $response = new Response('', 200, $headers);
        
        if ($ajax){
            return $this->container->get('templating')->renderResponse('FOSMessageBundle:Message:thread_ajax.html.twig', array(
                'form'          => $form->createView(),
                'thread'        => $thread,
                'message_types' => $messageTypesArr,
                'user'          => $user,
                'charter'       => $charter,
                'ajax'          => $ajax
            ), $response);
        } else {
            return $this->container->get('templating')->renderResponse('FOSMessageBundle:Message:thread.html.twig', array(
                'form'          => $form->createView(),
                'thread'        => $thread,
                'message_types' => $messageTypesArr,
                'user'          => $user,
                'charter'       => $charter,
                'ajax'          => $ajax
            ), $response);
        }
    }

    /**
     * Create a new message thread
     *
     * @return Response
     */
    public function newThreadAction()
    {
        
        $request    = $this->container->get('request');
        $ajax       = $request->isXmlHttpRequest();
        $em         = $this->container->get('doctrine.orm.entity_manager');
        $users      = $em->getRepository('ZizooUserBundle:User')->findAll();
        
        $newMessage = new NewThreadMultipleMessage(new ArrayCollection($users));
        
        //$form = $this->container->get('zizoo_message.new_thread_form.factory')->create($newMessage);
        $form = $this->container->get('fos_message.new_thread_form.factory')->create($newMessage);
       
        $formHandler = $this->container->get('fos_message.new_thread_form.handler');

        try {
            $message = $formHandler->process($form);
            if ($message) {
                return new RedirectResponse($this->container->get('router')->generate('fos_message_thread_view', array(
                    'threadId' => $message->getThread()->getId()
                )));
            }
        } catch (\InvalidArgumentException $e){
            $this->container->get('session')->getFlashBag()->add('error', $e->getMessage());
            return new RedirectResponse($this->container->get('router')->generate('ZizooMessageBundle_new_thread'));
        }
        

        if ($ajax){
            return $this->container->get('templating')->renderResponse('FOSMessageBundle:Message:newThread_ajax.html.twig', array(
                'form'      => $form->createView(),
                'data'      => $form->getData(),
                'ajax'      => $ajax
            ));
        } else {
            return $this->container->get('templating')->renderResponse('FOSMessageBundle:Message:newThread.html.twig', array(
                'form'      => $form->createView(),
                'data'      => $form->getData(),
                'ajax'      => $ajax
            ));
        }
    }

    function deleteFormAction($threadId=null, $ajax=false){
        $user       = $this->container->get('security.context')->getToken()->getUser();
        $request    = $this->container->get('request');
        if (!$ajax) $ajax = $request->isXmlHttpRequest();
        
        
        $defaultData = array(   'thread_id'    => $threadId?$threadId:''
                                );
        $formFactory    = $this->container->get('form.factory');
        $form           = $formFactory->createBuilder('form', $defaultData)
                            ->add('thread_id', 'hidden')
                            ->getForm();
        
        // If submit
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()){
                $data = $form->getData();
                $threadId = null;
                if (array_key_exists('thread_id', $data)){
                    $threadId = $data['thread_id'];
                }
                $thread = $this->getProvider()->getThread($threadId);
                $this->container->get('fos_message.deleter')->markAsDeleted($thread);
                $this->container->get('fos_message.thread_manager')->saveThread($thread);
                if ($ajax){
                    return new Response();
                } else {
                    return new RedirectResponse($this->container->get('router')->generate('fos_message_inbox'));
                }
            }
        }
        
        if ($ajax){
            return $this->container->get('templating')->renderResponse('ZizooMessageBundle:Message:delete_thread_ajax.html.twig', array(    'form'      => $form->createView(),
                                                                                                                                            'ajax'      => $ajax));
        } else {
            return $this->container->get('templating')->renderResponse('ZizooMessageBundle:Message:delete_thread.html.twig', array( 'form'      => $form->createView(),
                                                                                                                                    'ajax'      => $ajax));
        }
    }
    
    /**
     * Deletes a thread
     *
     * @return Response
     */
    public function deleteAction($threadId)
    {
        $thread = $this->getProvider()->getThread($threadId);
        $this->container->get('fos_message.deleter')->markAsDeleted($thread);
        $this->container->get('fos_message.thread_manager')->saveThread($thread);

        return new RedirectResponse($this->container->get('router')->generate('fos_message_inbox'));
    }

    /**
     * Searches for messages in the inbox and sentbox
     *
     * @return Response
     */
    public function searchAction()
    {
        $query = $this->container->get('fos_message.search_query_factory')->createFromRequest();
        $threads = $this->container->get('fos_message.search_finder')->find($query);

        return $this->container->get('templating')->renderResponse('FOSMessageBundle:Message:search.html.twig', array(
            'query' => $query,
            'threads' => $threads
        ));
    }
    
    public function markReadAction($threadId, $read)
    {
        $user       = $this->container->get('security.context')->getToken()->getUser();
        $request    = $this->container->get('request');
        $ajax       = $request->isXmlHttpRequest();
        $read       = $read=='true';
        
        $thread = $this->getProvider()->getThread($threadId);
        $thread->setIsReadByParticipant($user, $read);
        
        if ($ajax){
            return new Response();
        } else {
            return $this->redirect($this->generateUrl('fos_message_inbox'));
        }
    }
    
    /**
     *
     * @param ParticipantInterface $participant
     * @return Builder a query builder suitable for pagination
     */
    private function getParticipantThreadsQueryBuilder(ParticipantInterface $participant)
    {
        $em         = $this->container->get('doctrine.orm.entity_manager');
        $repository = $em->getRepository('ZizooMessageBundle:Thread');
        $qb         = $repository->createQueryBuilder('t');
        
        $qb->innerJoin('t.metadata', 'tm')
            ->innerJoin('tm.participant', 'p')

            // the participant is in the thread participants
            ->andWhere('p.id = :user_id')
            ->setParameter('user_id', $participant->getId())

            // the thread does not contain spam or flood
            ->andWhere('t.isSpam = :isSpam')
            ->setParameter('isSpam', false, \PDO::PARAM_BOOL)

            // the thread is not deleted by this participant
            ->andWhere('tm.isDeleted = :isDeleted')
            ->setParameter('isDeleted', false, \PDO::PARAM_BOOL)

            // there is at least one message written by an other participant
            //->andWhere('tm.lastMessageDate IS NOT NULL')

            // sort by date of last message written by an other participant
            //->orderBy('tm.lastMessageDate', 'DESC')
        ;
        return $qb;
    }
    
    /**
     * Gets the provider service
     *
     * @return ProviderInterface
     */
    protected function getProvider()
    {
        return $this->container->get('fos_message.provider');
    }
    
    
    
    
}
