<?php

namespace Zizoo\EventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EventController extends Controller
{
    public function indexAction()
    {
        $container      = $this->container;
        $em             = $container->get('doctrine.orm.entity_manager');
        $eventService   = $container->get('zizoo_event.event_service');
        
        $now = new \DateTime();
        
        $possibleEvents = $em->getRepository('ZizooEventBundle:event')->getPossibleRunEvents($now, 3);
        
        foreach ($possibleEvents as $event){
            try {
                if ($eventService->eventRunnable($event, $now)){
                    $command = $this->getApplication()->find($event->getCommand());
                    $arguments = array_merge(array('command' => $event->getCommand()), $event->getArguments());
                    $input = new ArrayInput($arguments);
                    $returnCode = $command->run($input, $output);
                    $event->setResult($returnCode);
                    $event->setLastRun($now);
                    $em->persist($event);
                    $em->flush();
                }
            } catch (\Exception $e){
                var_dump($e->getMessage());
            }
        }
    }
}
