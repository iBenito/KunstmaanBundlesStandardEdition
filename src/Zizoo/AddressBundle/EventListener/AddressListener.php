<?php
namespace Zizoo\AddressBundle\EventListener;

use Zizoo\AddressBundle\Entity\AddressBase as Address;
use Zizoo\AddressBundle\Service\AddressService;

use Doctrine\ORM\Event\LifecycleEventArgs;

class AddressListener
{
    protected $addressService;
    
    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }
       
    
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        
        if ($entity instanceof Address) {
            if (!$entity->getLat() || !$entity->getLng()){
                $this->addressService->fetchGeo($entity);
            }
        }
        
    }
    
    
    public function getSubscribedEvents() {
        return array(
            Events::prePersist
        );
    }
}
?>
