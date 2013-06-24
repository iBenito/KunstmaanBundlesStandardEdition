<?php
// src/Zizoo/ProfileBundle/EventListener/ProfileListener.php
namespace Zizoo\ProfileBundle\EventListener;

use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\ProfileBundle\Entity\ProfileAvatar;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Gd\Imagine;

use Liip\ImagineBundle\Imagine\Cache\CacheClearer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;


class ProfileAvatarListener
{
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    private function upload(ProfileAvatar $avatar, EntityManager $em)
    {
        $x1 = $avatar->getX1();
        $y1 = $avatar->getY1();
        $x2 = $avatar->getX2();
        $y2 = $avatar->getY2();
        $w  = $avatar->getW();
        $h  = $avatar->getH();
        
        if ($x1!==null && $y1!==null && $x2!==null && $y2!==null && $w!==null && $h!==null){
            $imagine = new Imagine();
            $path = $avatar->getAbsolutePath();
            $image = $imagine->open($path);
            $image->crop(new Point($x1, $y1), new Box($w, $h))
                    ->save($path);
            
            $liipImageCacheManager = $this->container->get('liip_imagine.cache.manager');
            $liipImagineFilterSets = $this->container->getParameter('liip_imagine.filter_sets');
            foreach ($liipImagineFilterSets as $filterSetName => $filterSetData){
                $liipImageCacheManager->remove($avatar->getWebPath(), $filterSetName);
            }
        }
  
    }
    

    public function postPersist(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getEntity();
        if ($entity instanceof ProfileAvatar) {
            $this->upload($entity, $em);
        }
    }
    
    public function postUpdate(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getEntity();
        if ($entity instanceof ProfileAvatar) {
            $this->upload($entity, $em);
        }
    }
    
    public function preRemove(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getEntity();
        if ($entity instanceof ProfileAvatar) {
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
