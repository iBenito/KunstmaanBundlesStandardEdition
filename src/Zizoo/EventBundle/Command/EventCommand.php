<?php
namespace Zizoo\EventBundle\Command;

use Zizoo\EventBundle\Entity\Event;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class EventCommand extends ContainerAwareCommand
{
    const RESPONSE_SUCCESS          = 0;
    
    protected function configure()
    {
        $this
            ->setName('zizoo:events')
            ->setDescription('Check and trigger Zizoo events');
            //->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
            //->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container      = $this->getContainer();
        $em             = $container->get('doctrine.orm.entity_manager');
        $eventService   = $container->get('zizoo_event.event_service');
        
        $now = new \DateTime();
        
        $possibleEvents = $em->getRepository('ZizooEventBundle:event')->getPossibleRunEvents($now, 3);
        
        foreach ($possibleEvents as $event){
            try {
                if ($eventService->eventRunnable($event, $now)){
                    $event->setStatus(Event::STATUS_RUNNING);
                    $em->persist($event);
                    
                    $command = $this->getApplication()->find($event->getCommand());
                    $arguments = array_merge(array('command' => $event->getCommand()), $event->getParameters());
                    $input = new ArrayInput($arguments);
                    $returnCode = $command->run($input, $output);
                    $event->setResult($returnCode);
                    if ($returnCode!=0){
                        $event->setRetry($event->getRetry()+1);
                    }
                    $event->setLastRun($now);
                    
                    if ($eventService->willRunAgain($event, $now)){
                        $event->setStatus(Event::STATUS_NEW);
                    } else {
                        $event->setStatus(Event::STATUS_COMPLETE);
                    }
                    
                    $em->persist($event);
                    $em->flush();
                }
            } catch (\Exception $e){
                var_dump($e->getMessage());
            }
        }
        return 4;
    }
}
?>
