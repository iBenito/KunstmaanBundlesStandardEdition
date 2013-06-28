<?php
// src/Zizoo/ProfileBundle/Form/EventListener/BoatSubscriber.php
namespace Zizoo\ProfileBundle\Form\EventListener;

use Zizoo\ProfileBundle\Entity\ProfileAvatar;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProfileSubscriber implements EventSubscriberInterface
{
    protected $em;
    protected $uploadAvatar;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(   FormEvents::BIND        => 'bind',
                        FormEvents::POST_BIND   => 'postBind');
    }

    public function postBind(FormEvent $event)
    {
        

    }
    
    public function bind(FormEvent $event)
    {
        
        
    }
}
?>
