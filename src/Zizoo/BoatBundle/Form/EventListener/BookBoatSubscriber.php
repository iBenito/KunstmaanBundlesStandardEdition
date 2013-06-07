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
        return array(FormEvents::POST_BIND => 'postBind');
    }

    public function postBind(FormEvent $event)
    {
        return;
        $data = $event->getData();
        $form = $event->getForm();

        // During form creation setData() is called with null as an argument
        // by the FormBuilder constructor. You're only concerned with when
        // setData is called with an actual Entity object in it (whether new
        // or fetched with Doctrine). This if statement lets you skip right
        // over the null condition.
        if (null === $data ) {
            return;
        }

        $reservationAgent = $this->container->get('zizoo_reservation_reservation_agent');
        try {
            $reservationRange = $data->getReservationRange();
            //$reservationRange = $data['reservation_range'];
            $from   = $reservationRange?$reservationRange->getReservationFrom():null;
            //$from = $reservationRange['reservation_from'];
            $until  = $reservationRange?$reservationRange->getReservationTo():null;
            //$until = $reservationRange['reservation_to'];
            $totalPrice = $reservationAgent->getTotalPrice($data->getBoat(), $from, $until, true, true);
            $data->setTotalPrice($totalPrice['total']);
            //$form->setData($data);
            //$a = $form->getViewData();
        } catch (\Zizoo\ReservationBundle\Exception\InvalidReservationException $e){
            $totalPrice = 0;
        }
        
    }
}
?>
