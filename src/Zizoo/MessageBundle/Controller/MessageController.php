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
 
    public function inboxAction()
    {
        $request    = $this->container->get('request');
        $user       = $this->container->get('security.context')->getToken()->getUser();
        
        $routes = $request->query->get('routes');
        
        $qb = $this->getProvider()->getThreadsQueryBuilder();
        
        $page     = $request->query->get('page', 1);
        $pageSize = $request->query->get('page_size', 10);
        
        $paginator  = $this->container->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $page/*page number*/,
            $pageSize/*limit per page*/
        );
        $pagination->setCustomParameters(array('itemName' => 'Threads'));
        
        return $this->container->get('templating')->renderResponse('ZizooMessageBundle:Message:inbox.html.twig', array(
            'pagination'        => $pagination,
            'user'              => $user,
            'routes'            => $routes
        ));
    }
    
    /**
     * Displays a thread, also allows to reply to it
     *
     * @param strind $threadId the thread id
     * @return Response
     */
    public function threadAction($threadId, $view='user')
    {
        if (!$threadId){
            return new RedirectResponse($this->container->get('router')->generate('fos_message_inbox'));
        }
        $request                = $this->container->get('request');
        
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
            $url = $this->container->get('router')->generate($routes['view_thread_route'], array('id' => $message->getThread()->getId()), array(
                'threadId'      => $message->getThread()->getId(),
                'message_types' => $messageTypesArr,
                'user'          => $user,
                'view'          => $view
            ));
            return new RedirectResponse($url);
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
        
        return $this->container->get('templating')->renderResponse('FOSMessageBundle:Message:thread.html.twig', array(
            'form'          => $form->createView(),
            'thread'        => $thread,
            'message_types' => $messageTypesArr,
            'user'          => $user,
            'charter'       => $charter,
            'routes'        => $routes,
            'view'          => $view
        ), $response);
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
     * Gets the provider service
     *
     * @return ProviderInterface
     */
    protected function getProvider()
    {
        return $this->container->get('zizoo_message.provider');
    }
    
    
    
    
}
