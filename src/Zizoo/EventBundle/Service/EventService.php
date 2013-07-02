<?php
namespace Zizoo\EventBundle\Service;

use Zizoo\EventBundle\Entity\Event;

use Cron;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class EventService {
    
    
    
    private $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
  
    private function getCronExpression(Event $event)
    {
        return $event->getMinute() . ' ' . $event->getHour() . ' ' . $event->getDayOfMonth() . ' ' . $event->getMonth() . ' ' . $event->getDayOfMonth() . ' ' . $event->getYear();
    }
    
    public function eventRunnable(Event $event, \DateTime $now)
    {
        $status = $event->getStatus();
        switch ($status){
            case Event::STATUS_RUNNING:
                return false;
                break;
            case Event::STATUS_COMPLETE:
                if ($event->getRetries() < $this->container->getParameter('zizoo_event.event_retries') && $event->getResponse() != Event::RESPONSE_SUCCESS){
                    return true;
                }
                break;
            default:
                // Do nothing:
                break;
        }
        
        $cronExpression = $this->getCronExpression($event);
        
        $cron = Cron\CronExpression::factory($cronExpression);
        
        if ($cron->isDue($now)){
            return true;
        } else {
            $lastRunDate = $cron->getPreviousRunDate($now, 0);
            if (!$lastRunDate) return false;
            if (!$event->getLastRun()) true;
            return $lastRunDate > $event->getLastRun();
        }
        
    }
    
    public function willRunAgain(Event $event, \DateTime $now)
    {
        $cronExpression = $this->getCronExpression($event);
        
        $cron = Cron\CronExpression::factory($cronExpression);
        
        return $cron->getNextRunDate($now, 1);
    }
    
    
    
}


?>
