<?php
// src/Zizoo/ProfileBundle/EventListener/ProfileListener.php
namespace Zizoo\ProfileBundle\EventListener;

use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\ProfileBundle\Entity\ProfileAvatar;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;


class ProfileListener
{
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    private function upload(Profile $profile, EntityManager $em)
    {
//        $avatars = $profile->getAvatar();
//        $i = $avatars->count();
//        foreach ($avatars as $avatar){
//            $avatar->setOrder($i--);
//            $em->persist($avatar);
//        }
        
        if (null === $profile->getFile()) {
            $avatars = $profile->getAvatar();
            foreach ($avatars as $avatar){
                $em->persist($avatar);
            }
            $em->flush();
            return;
        } 

        $avatar = new ProfileAvatar();
        $avatar->setProfile($profile);
        $profile->addAvatar($avatar);
                
        $em->persist($avatar);
        
        $path = $profile->getFile()->guessExtension();

        $avatar->setPath($path);

        $em->flush();
    }
    
    public function postPersist(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getEntity();
        if ($entity instanceof Profile) {
            $this->upload($entity, $em);
        }
    }
    
    public function postUpdate(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getEntity();
        if ($entity instanceof Profile) {
            $this->upload($entity, $em);
        }
    }
    
    
    
    public function getSubscribedEvents() {
        return array(
            Events::postPersist,
            Events::postUpdate,
        );
    }
}
?>
