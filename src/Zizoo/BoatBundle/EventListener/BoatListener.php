<?php
namespace Zizoo\BoatBundle\EventListener;

use Zizoo\BoatBundle\Entity\Boat;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class BoatListener
{
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
       
    private function checkBoatCompleteness(Boat $boat)
    {   
        $completeErrors = $this->container->get('boat_service')->boatCompleteValidate($boat);
        $complete = count($completeErrors)==0;
        
        $boat->setComplete($complete);
        if ($complete !== true) $boat->setActive(false);
    }
    
    private function handleBoatEntity(Boat $boat)
    {
        $boat->updateLowestAndHighestPrice();
        
        $this->checkBoatCompleteness($boat);
    }
    
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Boat) {
            $this->handleBoatEntity($entity);
        }
    }
    
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Boat) {
            $this->handleBoatEntity($entity);
        }
    }
    
    
    public function getSubscribedEvents() {
        return array(
            Events::prePersist,
            Events::preUpdate,
        );
    }
}
?>
