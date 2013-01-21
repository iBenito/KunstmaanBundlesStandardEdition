<?php

namespace Zizoo\MessageBundle\Controller;

use Zizoo\MessageBundle\Entity\Message;
use Zizoo\MessageBundle\Form\Type\DeleteMessageType;
use Zizoo\MessageBundle\Extensions\DoctrineExtensions\Query\Mysql\GroupConcat;
use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\JqGridCustomBundle\Grid\Grid;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\NotBlank;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Collections\ArrayCollection;

class MessageController extends Controller
{
    /**
     * @Template()
     */
    public function inboxAction()
    {
        $user           = $this->getUser();
        $profile        = $user->getProfile();
        $request        = $this->getRequest();
        $showThreads    = $request->query->get('show_threads', false);
        $em = $this->getDoctrine()->getEntityManager();

        $qb = $em->getRepository('ZizooMessageBundle:Message')->getInboxMessagesQueryBuilder($profile, $showThreads);
        
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
        $grid->setRouteForced($this->get('router')->generate('inbox', array('show_threads' => $showThreads)));
        $grid->setHideIfEmpty(false);

        //MANDATORY
        $grid->setSource($qb);

        $formatFnc = "function(cellValue, options, rowObject){"
                                ."var cellClass = 'unread';"
                                ."if (rowObject.cell[7]) cellClass='read';"
                                ."return '<div class=\"'+cellClass+'\">'+cellValue+'</div>'"
                                ."}";
        
        $formatTypeFnc = "function(cellValue, options, rowObject){"
                                ."var cellClass = 'unread';"
                                ."if (rowObject.cell[7]) cellClass='read';"
                                ."switch (cellValue){"
                                ."  case 0:"
                                ."      cellValue = '".Message::getTypeToString(0)."';"
                                ."      break;"
                                ."  case 1:"
                                ."      cellValue = '".Message::getTypeToString(1)."';"
                                ."      break;"
                                ."  case 2:"
                                ."      cellValue = '".Message::getTypeToString(2)."';"
                                ."      break;"
                                ."  case 3:"
                                ."      cellValue = '".Message::getTypeToString(3)."';"
                                ."      break;"
                                ."  case 4:"
                                ."      cellValue = '".Message::getTypeToString(4)."';"
                                ."      break;"
                                ."  case 5:"
                                ."      cellValue = '".Message::getTypeToString(5)."';"
                                ."      break;"
                                ."}"
                                ."return '<div class=\"'+cellClass+'\">'+cellValue+'</div>'"
                                ."}";
        
        $extraJS = "function messageOpen(msgId, type){"
                    ."  switch (type){"
                    ."      case '0':"
                    ."          url = '".$this->generateUrl('open_received_message')."/'+msgId;"
                    ."          window.location.href = url;"
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
                    ."      case '5':"
                    ."          url = '".$this->generateUrl('open_received_message')."/'+msgId;"
                    ."          window.location.href = url;"
                    ."          break;"
                    ."  }"
                    ."}";
        
        //COLUMNS DEFINITION
        $grid->addColumn('ID', array('name' => 'id', 'jsonmap' => 'cell.0','index' => 'm.id', 'hidden' => true, 'sortable' => false, 'search' => false));
        $grid->addColumn('RecipientID', array('name' => 'recipient_id', 'jsonmap' => 'cell.1', 'hidden' => true, 'sortable' => false, 'search' => false));
        $grid->addColumn('Subject', array('name' => 'subject', 'jsonmap' => 'cell.2', 'index' => 'm.subject', 'width' => '250', 'search' => true, 'formatter' => $formatFnc));
        $grid->addColumn('From', array('name' => 'sender', 'jsonmap' => 'cell.3', 'index' => 'sp.lastName, sp.firstName, su.email', 'width' => '150', 'search' => false, 'formatter' => $formatFnc));
        $grid->addColumn('Received', array('name' => 'sent', 'jsonmap' => 'cell.4.date', 'index' => 'm.sent', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => true, 'formatter' => $formatFnc));
        $grid->addColumn('Type', array('name' => 'type', 'jsonmap' => 'cell.5', 'index' => 'm.type', 'width' => '100', 'search' => false, 'formatter' => $formatTypeFnc));
        $grid->addColumn('TypeInt', array('name' => 'type_int', 'jsonmap' => 'cell.5', 'index' => 'm.type', 'hidden' => true, 'search' => false));
        $grid->addColumn('ReadDate', array('name' => 'read', 'jsonmap' => 'cell.7', 'hidden' => true, 'sortable' => false, 'search' => false));

        $grid->setExtraParams(array( 'show_threads'         => ($showThreads?'checked="checked"':''),
                                        'loadComplete'      => 'loadComplete',
                                        'url_threads'       => $this->get('router')->generate('inbox', array('show_threads' => true)),
                                        'url_no_threads'    => $this->get('router')->generate('inbox', array('show_threads' => false)),
                                        'extraJS'           => $extraJS));
        
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
        $user           = $this->getUser();
        $profile        = $user->getProfile();
        $request        = $this->getRequest();
        $showThreads    = $request->query->get('show_threads', false);
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $qb = $em->getRepository('ZizooMessageBundle:Message')->getSentQueryBuilder($profile, $showThreads);

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
        $grid->setRouteForced($this->get('router')->generate('sent', array('show_threads' => $showThreads)));
        $grid->setHideIfEmpty(false);

        //MANDATORY
        $grid->setSource($qb);

        //COLUMNS DEFINITION
        $grid->addColumn('ID', array('name' => 'message_id', 'jsonmap' => 'cell.0','index' => 'm.id', 'hidden' => true, 'sortable' => false, 'search' => false));
        $grid->addColumn('Subject', array('name' => 'subject', 'jsonmap' => 'cell.1', 'index' => 'm.subject', 'width' => '250', 'search' => true));
        $grid->addColumn('To', array('name' => 'receiver', 'jsonmap' => 'cell.2', 'index' => 'sp.lastName, sp.firstName, su.email', 'width' => '150', 'search' => false));
        $grid->addColumn('Received', array('name' => 'sent', 'jsonmap' => 'cell.3.date', 'index' => 'm.sent', 'formatter' => 'date', 'formatoptions' => array( 'srcformat' => 'Y-m-d H:i:s', 'newformat' => 'd/m/Y H:i' ), 'datepicker' => true));
               
        $grid->setExtraParams(array( 'show_threads'         => ($showThreads?'checked="checked"':''),
                                        'loadComplete'      => 'loadComplete',
                                        'url_threads'       => $this->get('router')->generate('sent', array('show_threads' => true)),
                                        'url_no_threads'    => $this->get('router')->generate('sent', array('show_threads' => false)),
                                        'extraJS'           => ''));
        
        return ($grid->render());
    }
    
    public function deleteSentMessageAction($messageId=null, $ajax=false){
        $user = $this->getUser();
        $profile = $user->getProfile();
        $request = $this->getRequest();
        if (!$ajax) $ajax    = $request->isXmlHttpRequest();
        $em = $this->getDoctrine()->getEntityManager();
        
        if (!$ajax){
            if (!$messageId) {
                $f = $request->request->get('form', null);
                if ($f && array_key_exists('message_id', $f)) $messageId = $f['message_id'];
            }
            if (!$messageId){
                return $this->redirect($this->generateUrl('sent'));
            }
            $message = $em->getRepository('ZizooMessageBundle:message')->findOneById($messageId);
            if (!$message){
                return $this->redirect($this->generateUrl('sent'));
            }
            if ($profile->getId()!=$message->getSenderProfile()->getId()){
                return $this->redirect($this->generateUrl('sent'));
            }
        } else {
            $message = null;
            if ($messageId){
                $message = $em->getRepository('ZizooMessageBundle:message')->findOneById($messageId);
            }
            if (!$message) $message = new Message();
        }
        $trans = $this->get('translator');
        //$form = $this->createForm(new DeleteMessageType($message), null, array('confirm_delete_label' => $trans->trans('zizoo_message.label.confirm_sent_message_delete')));
        $defaultData = array('message_id' => $messageId?$messageId:'');
        $form = $this->createFormBuilder($defaultData)
                        ->add('message_id', 'hidden')
                        ->getForm();
        
        // If submit
        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()){
                $messenger = $this->get('messenger');
                $messageId = $request->request->get('message_id', null);

                if (!$messageId) {
                    $f = $request->request->get('form', null);
                    if ($f && array_key_exists('message_id', $f)) $messageId = $f['message_id'];
                }
                
                
                if (!$messageId){
                    throw $this->createNotFoundException('This message does not exist');
                }
                $message = $em->getRepository('ZizooMessageBundle:message')->findOneById($messageId);
                if (!$message){
                    throw $this->createNotFoundException('This message does not exist');
                }
                $message = $messenger->deleteSentMessage($profile, $message);
                if ($message){
                    return $this->redirect($this->generateUrl('message_deleted'));
                } else {
                    return $this->redirect($this->generateUrl('sent'));
                }
            }
        }
        
        if ($ajax){
            return $this->render('ZizooMessageBundle:Message:delete_sent_message_ajax.html.twig', array( 'form'      => $form->createView(),
                                                                                                    'ajax'      => $ajax));
        } else {
            return $this->render('ZizooMessageBundle:Message:delete_sent_message.html.twig', array( 'form'      => $form->createView(),
                                                                                                    'ajax'      => $ajax));
        }
    }
    
    public function deleteReceivedMessageAction($messageId=null, $ajax=false){
        $user = $this->getUser();
        $profile = $user->getProfile();
        $request = $this->getRequest();
        if (!$ajax) $ajax    = $request->isXmlHttpRequest();
        $em = $this->getDoctrine()->getEntityManager();
        
        if (!$ajax){
            if (!$messageId) {
                $f = $request->request->get('form', null);
                if ($f && array_key_exists('message_id', $f)) $messageId = $f['message_id'];
            }
            if (!$messageId){
                return $this->redirect($this->generateUrl('inbox'));
            }
            $message = $em->getRepository('ZizooMessageBundle:message')->findOneById($messageId);
            if (!$message){
                return $this->redirect($this->generateUrl('inbox'));
            }
            $userIsRecipient = false;
            $recipients = $message->getRecipients();
            foreach ($recipients as $recipient){
                if ($profile->getId()==$recipient->getRecipientProfile()->getId()){
                    $userIsRecipient = true;
                    break;
                }
            }
            if (!$userIsRecipient){
                return $this->redirect($this->generateUrl('inbox'));
            }
        } else {
            $message = null;
            if ($messageId){
                $message = $em->getRepository('ZizooMessageBundle:message')->findOneById($messageId);
            }
            if (!$message) $message = new Message();
        }
        
        
        $trans = $this->get('translator');
        //$form = $this->createForm(new DeleteMessageType($message), null, array('confirm_delete_label' => $trans->trans('zizoo_message.label.confirm_received_message_delete')));
        $defaultData = array(   'message_id'    => $messageId?$messageId:'');
        $form = $this->createFormBuilder($defaultData)
                        ->add('message_id', 'hidden')
                        //->add('delete_thread', 'checkbox', array( 'label' => $trans->trans('zizoo_message.label.delete_thread')))
                        ->getForm();
        
        // If submit
        
        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()){
                $messenger = $this->get('messenger');
                $messageId = $request->request->get('message_id', null);
                
                
                $f = $request->request->get('form', null);
                if ($f && array_key_exists('message_id', $f)) $messageId = $f['message_id'];
                //if ($f && array_key_exists('delete_thread', $f)) $deleteThread = $f['delete_thread'];
                
                if (!$messageId){
                    throw $this->createNotFoundException('This message does not exist');
                }
                $message = $em->getRepository('ZizooMessageBundle:message')->findOneById($messageId);
                if (!$message){
                    throw $this->createNotFoundException('This message does not exist');
                }
                $message = $messenger->deleteReceivedMessage($profile, $message);
                if ($message){
                    return $this->redirect($this->generateUrl('message_deleted'));
                } else {
                    return $this->redirect($this->generateUrl('inbox'));
                }
            }
        }

      
        
        if ($ajax){
            return $this->render('ZizooMessageBundle:Message:delete_received_message_ajax.html.twig', array( 'form'      => $form->createView(),
                                                                                                        'ajax'      => $ajax));
        } else {
            return $this->render('ZizooMessageBundle:Message:delete_received_message.html.twig', array( 'form'      => $form->createView(),
                                                                                                        'ajax'      => $ajax));
        }
    }
    
    public function messageDeletedAction(){
        return $this->render('ZizooMessageBundle:Message:delete_message_deleted.html.twig');
    }
    
    public function markReceivedMessageAction($messageId, $read){
        $user = $this->getUser();
        $profile = $user->getProfile();
        $request = $this->getRequest();
        $ajax = $request->isXmlHttpRequest();
        $read = $read=='true';
        $em = $this->getDoctrine()->getEntityManager();
        
        
        if (!$messageId) {
            $f = $request->request->get('form', null);
            if ($f && array_key_exists('message_id', $f)) $messageId = $f['message_id'];
        }
        if (!$messageId){
           throw $this->createNotFoundException('This message does not exist');
        }
        $message = $em->getRepository('ZizooMessageBundle:message')->findOneById($messageId);
        if (!$message){
            throw $this->createNotFoundException('This message does not exist');
        }
        $userIsRecipient = false;
        $recipients = $message->getRecipients();
        foreach ($recipients as $recipient){
            if ($profile->getId()==$recipient->getRecipientProfile()->getId()){
                $userIsRecipient = true;
                break;
            }
        }
        if (!$userIsRecipient){
            throw $this->createNotFoundException('This message does not exist');
        }
        $messenger = $this->get('messenger');
        $message = $messenger->markReceivedMessage($profile, $message, $read);
       
        if ($ajax){
            return new Response();
        } else {
            return $this->redirect($this->generateUrl('inbox'));
        }
        
    }
    
    private function getLastIncomingMessageProfile($thread, Profile $profile){
        $threadArr = array_reverse($thread);
        foreach ($threadArr as $message){
            if ($message->getSenderProfile()->getId()!=$profile->getId()){
                return $message->getSenderProfile();
            }
        }
        return null;
    }
    
    public function openReceivedMessageAction($messageId=null, $ajax=false){
        $user = $this->getUser();
        $profile = $user->getProfile();
        $request = $this->getRequest();
        if (!$ajax) $ajax = $request->isXmlHttpRequest();
        
        $em = $this->getDoctrine()->getEntityManager();
        
        if (!$messageId) {
            $f = $request->request->get('form', null);
            if ($f && array_key_exists('message_id', $f)) $messageId = $f['message_id'];
        }
        if (!$messageId){
           throw $this->createNotFoundException('This message does not exist');
        }
        $message = $em->getRepository('ZizooMessageBundle:message')->findOneBy(array( 'id' => $messageId));
        if (!$message){
            throw $this->createNotFoundException('This message does not exist');
        }
    
        $userIsRecipient = false;
        $recipients = $message->getRecipients();
        foreach ($recipients as $recipient){
            if ($profile->getId()==$recipient->getRecipientProfile()->getId() && $recipient->getRecipientKeep()){
                $userIsRecipient = true;
                break;
            }
        }
        if (!$userIsRecipient){
            throw $this->createNotFoundException('This message does not exist');
        }
        
        $thread = $em->getRepository('ZizooMessageBundle:Message')->getMessageThread($message);
        $to = $this->getLastIncomingMessageProfile($thread, $profile);
        if (!$to){
            throw $this->createNotFoundException('This message does not exist');
        }
        $lastMessage = $thread[count($thread) -1];
        $lastMessageId = $lastMessage->getId();

        $redirect = 'open_sent_message';
        if ($ajax){
             return $this->render('ZizooMessageBundle:Message:open_received_message_ajax.html.twig', array( 'thread'            => $thread,
                                                                                                            'message'           => $message,
                                                                                                            'profile'           => $profile,
                                                                                                            'to'                => $to->getId(),
                                                                                                            'last_message_id'   => $lastMessageId,
                                                                                                            'redirect'          => $redirect,
                                                                                                            'ajax'              => $ajax));
        } else {
            return $this->render('ZizooMessageBundle:Message:open_received_message.html.twig', array(   'thread'            => $thread,
                                                                                                        'message'           => $message,
                                                                                                        'profile'           => $profile,
                                                                                                        'to'                => $to->getId(),
                                                                                                        'last_message_id'   => $lastMessageId,
                                                                                                        'redirect'          => $redirect,
                                                                                                        'ajax'              => $ajax));
        }
    }
    
    public function openSentMessageAction($messageId=null, $ajax=false){
        $user = $this->getUser();
        $profile = $user->getProfile();
        $request = $this->getRequest();
        if (!$ajax) $ajax = $request->isXmlHttpRequest();
        
        $em = $this->getDoctrine()->getEntityManager();
        
        if (!$messageId) {
            $f = $request->request->get('form', null);
            if ($f && array_key_exists('message_id', $f)) $messageId = $f['message_id'];
        }
        if (!$messageId){
           throw $this->createNotFoundException('This message does not exist');
        }
        $message = $em->getRepository('ZizooMessageBundle:message')->findOneBy(array(   'id'            => $messageId,
                                                                                        'sender_keep'   => true));
        if (!$message){
            throw $this->createNotFoundException('This message does not exist');
        }

        if ($profile->getId()!=$message->getSenderProfile()->getId()){
            throw $this->createNotFoundException('This message does not exist');
        }
        
        $thread = $em->getRepository('ZizooMessageBundle:Message')->getMessageThread($message);
        $to = $this->getLastIncomingMessageProfile($thread, $profile);
        if (!$to){
            throw $this->createNotFoundException('This message does not exist');
        }
        $lastMessage = $thread[count($thread) -1];
        $lastMessageId = $lastMessage->getId();

        $redirect = 'open_sent_message';
        if ($ajax){
             return $this->render('ZizooMessageBundle:Message:open_sent_message_ajax.html.twig', array( 'thread'            => $thread,
                                                                                                        'message'           => $message,
                                                                                                        'profile'           => $profile,
                                                                                                        'to'                => $to->getId(),
                                                                                                        'last_message_id'   => $lastMessageId,
                                                                                                        'redirect'          => $redirect,
                                                                                                        'ajax'              => $ajax));
        } else {
            return $this->render('ZizooMessageBundle:Message:open_sent_message.html.twig', array(   'thread'            => $thread,
                                                                                                    'message'           => $message,
                                                                                                    'profile'           => $profile,
                                                                                                    'to'                => $to->getId(),
                                                                                                    'last_message_id'   => $lastMessageId,
                                                                                                    'redirect'          => $redirect,
                                                                                                    'ajax'              => $ajax));
        }
    }
    
    public function sendMessageAction($lastMessageId=null, $to=null, $ajax=false, $redirect=null){
        $user = $this->getUser();
        $profile = $user->getProfile();
        $request = $this->getRequest();
        if (!$ajax) $ajax = $request->isXmlHttpRequest();
        $em = $this->getDoctrine()->getEntityManager();
        
        $f = $request->request->get('form', null);
        if (!$lastMessageId) {
            if ($f && array_key_exists('reply_to_message_id', $f)) $lastMessageId = $f['reply_to_message_id'];
        }
        if (!$to) {
            if ($f && array_key_exists('to', $f)) $to = $f['to'];
        }
        
        if (!$to){
            throw $this->createNotFoundException('Error');
        }
        $toProfile = $em->getRepository('ZizooProfileBundle:Profile')->findOneById($to);
        if (!$toProfile){
            throw $this->createNotFoundException('Error');
        }
        
        if (!$redirect){
            if ($f && array_key_exists('redirect', $f)) $redirect = $f['redirect'];
        }
        
        $defaultData = array(   'reply_to_message_id'  => $lastMessageId?$lastMessageId:'',
                                'from'                 => $profile->getID(),
                                'to'                   => $toProfile->getId(),
                                'redirect'             => $redirect);

        $form = $this->createFormBuilder($defaultData)
                        ->add('from', 'hidden')
                        ->add('to', 'hidden')
                        ->add('reply_to_message_id', 'hidden')
                        ->add('redirect', 'hidden')
                        ->add('message_body', 'textarea', array(
                                                                'constraints' => array(
                                                                    new NotBlank()
                                                                ),
                                                            ))
                        ->getForm();
        
        if ($request->isMethod('POST')) {
            
            
            $form->bind($request);

            if ($form->isValid()){
                
                $body = "I'm an idiot.";
                $previous = null;
                $subject = null;
                
                $data = $form->getData();
                if (array_key_exists('message_body', $data)) $body = $data['message_body'];
                if (array_key_exists('reply_to_message_id', $data)){
                    $replyToId = $data['reply_to_message_id'];
                    $previous = $em->getRepository('ZizooMessageBundle:Message')->findOneById($replyToId);                    
                }
                if (!$previous) $subject = '';
                $messenger = $this->get('messenger');
                //Profile $sender, Profile $recipient, $body, $subject=null, Message $previous=null, $setRecipient=true
                $message = $messenger->sendMessageTo($profile, $toProfile, $body, $subject, $previous, false);
                if ($redirect){
                    return $this->redirect($this->generateUrl($redirect, array( 'messageId' => $message->getId())));
                } else {
                    return $this->redirect($this->generateUrl('inbox'));
                }
            } else {
                $validator = $this->get('validator');
                $errors = $validator->validate($form);
                $num_errors = $errors->count();
                
                for ($i=0; $i<$num_errors; $i++){
                    $msgTemplate = $errors->get($i)->getMessageTemplate();
                    $this->get('session')->getFlashBag()->add('notice', $msgTemplate);
                }
                if ($redirect){
                    return $this->redirect($request->headers->get('referer'));
                } else {
                    return $this->redirect($this->generateUrl('inbox'));
                }
            }
        }
        
        if ($ajax){
             return $this->render('ZizooMessageBundle:Message:send_message_ajax.html.twig', array(  'from'              => $profile->getId(),
                                                                                                    'to'                => $toProfile->getId(),
                                                                                                    'last_message_id'   => $lastMessageId,
                                                                                                    'form'              => $form->createView(),
                                                                                                    'redirect'          => $redirect,
                                                                                                    'ajax'              => $ajax));
        } else {
            return $this->render('ZizooMessageBundle:Message:send_message.html.twig', array(    'from'              => $profile->getId(),
                                                                                                'to'                => $toProfile->getId(),
                                                                                                'last_message_id'   => $lastMessageId,
                                                                                                'form'              => $form->createView(),
                                                                                                'redirect'          => $redirect,
                                                                                                'ajax'              => $ajax));
        }
    }
}
