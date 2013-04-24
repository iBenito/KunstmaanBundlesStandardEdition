<?php
// src/Zizoo/ReservationBundle/Form/EventListener/AddDenyMessageFieldSubscriber.php
namespace Zizoo\ReservationBundle\Form\EventListener;

use Zizoo\ReservationBundle\Form\Type\DenyReservationType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddDenyReservationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // During form creation setData() is called with null as an argument
        // by the FormBuilder constructor. You're only concerned with when
        // setData is called with an actual Entity object in it (whether new
        // or fetched with Doctrine). This if statement lets you skip right
        // over the null condition.
        if (null === $data) {
            return;
        }

        // check if there are overlapping reservation requests
        if (count($data->getOverlappingReservationRequests())>0) {
            $form->add('deny_reservation', new DenyReservationType(), array('label' => ' '));
        }
    }
}
?>
