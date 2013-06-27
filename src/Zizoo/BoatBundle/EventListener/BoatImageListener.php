<?php
// src/Zizoo/ProfileBundle/EventListener/ProfileListener.php
namespace Zizoo\BoatBundle\EventListener;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Entity\BoatImage;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Gd\Imagine;

use Liip\ImagineBundle\Imagine\Cache\CacheClearer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;


class BoatImageListener
{
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    private function upload(BoatImage $boatImage, EntityManager $em)
    {
        $x1 = $boatImage->getX1();
        $y1 = $boatImage->getY1();
        $x2 = $boatImage->getX2();
        $y2 = $boatImage->getY2();
        $w  = $boatImage->getW();
        $h  = $boatImage->getH();
        
        if ($x1!==null && $y1!==null && $x2!==null && $y2!==null && $w!==null && $h!==null){
            $imagine = new Imagine();
            $path = $boatImage->getAbsolutePath();
            $image = $imagine->open($path);
            $image->crop(new Point($x1, $y1), new Box($w, $h))
                    ->save($path);
            
            $liipImageCacheManager = $this->container->get('liip_imagine.cache.manager');
            $liipImagineFilterSets = $this->container->getParameter('liip_imagine.filter_sets');
            foreach ($liipImagineFilterSets as $filterSetName => $filterSetData){
                $liipImageCacheManager->remove($boatImage->getWebPath(), $filterSetName);
            }
        }
  
    }
    

    public function postPersist(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getEntity();
        if ($entity instanceof BoatImage) {
            $this->upload($entity, $em);
        }
    }
    
    public function postUpdate(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getEntity();
        if ($entity instanceof BoatImage) {
            $this->upload($entity, $em);
        }
    }
    
    public function preRemove(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getEntity();
        if ($entity instanceof BoatImage) {
            $entity->delete();
        }
    }
    
    
    
    public function getSubscribedEvents() {
        return array(
            Events::postPersist,
            Events::postUpdate,
            Events::preRemove,
        );
    }
}
?>
