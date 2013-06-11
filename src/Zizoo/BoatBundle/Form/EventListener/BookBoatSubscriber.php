<?php
// src/Zizoo/BoatBundle/Form/EventListener/AddTermsFieldSubscriber.php
namespace Zizoo\BoatBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\Container;

class BookBoatSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $container;

    public function __construct(FormFactoryInterface $factory, Container $container)
    {
        $this->factory      = $factory;
        $this->container    = $container;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $data   = $event->getData();
        $form   = $event->getForm();
        $em     = $this->container->get('doctrine.orm.entity_manager');
        
        $boat   = $em->getRepository('ZizooBoatBundle:Boat')->findOneById($data->getBoatId());
        
        if ($boat->getCrewOptional()){
            $form->add('crew', 'checkbox', array(   'required'      => true));
        }
        
    }
}
?>
