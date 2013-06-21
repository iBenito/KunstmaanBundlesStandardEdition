<?php
// src/Zizoo/ProfileBundle/EventListener/ProfileListener.php
namespace Zizoo\ProfileBundle\EventListener;

use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\ProfileBundle\Entity\ProfileAvatar;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Gd\Imagine;

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
        
        if ($avatar->getX1() && $avatar->getY1() && $avatar->getX2() && $avatar->getY1() && $avatar->getW() && $avatar->getH()){
            $imagine = new Imagine();
            $path = $avatar->getAbsolutePath();
            $image = $imagine->open($path);
            $image->crop(new Point($avatar->getX1(), $avatar->getY1()), new Box($avatar->getW(), $avatar->getH()))
                    ->save($path);
        }
        
        $profile = $avatar->getProfile();
        if (null === $profile->getFile()) {
            return;
        }

        // check if we have an old image
//        if (isset($this->temp)) {
//            // delete the old image
//            unlink($this->temp);
//            // clear the temp image path
//            $this->temp = null;
//        }

        // you must throw an exception here if the file cannot be moved
        // so that the entity is not persisted to the database
        // which the UploadedFile move() method does
        

        $path = $avatar->getPath();
        $profile->getFile()->move(
            $avatar->getUploadRootDir(),
            $avatar->getId().'.'.$path
        );

        $profile->setFile(null);
        
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
