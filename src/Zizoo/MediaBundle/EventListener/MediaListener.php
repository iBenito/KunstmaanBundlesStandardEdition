<?php
namespace Zizoo\MediaBundle\EventListener;

use Zizoo\MediaBundle\Entity\Media;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Event\LifecycleEventArgs;

class MediaListener
{
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
   
    
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Media) {
            $file = $entity->getFile();
            try{
                $file->move(
                    $entity->getUploadRootDir(),
                    $entity->getPathAndName());
            } catch (FileException $e){
                throw new \Exception('File could not be moved: ' . $e->getMessage());
            }
        }
    }
    
    
    public function getSubscribedEvents() {
        return array(
            Events::postPersist
        );
    }
}
?>
