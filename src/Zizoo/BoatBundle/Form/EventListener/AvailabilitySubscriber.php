<?php
namespace Zizoo\BoatBundle\Form\EventListener;

use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\ReservationBundle\Form\Type\DenyReservationType;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AvailabilitySubscriber implements EventSubscriberInterface
{
    private $container;


    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_BIND => 'preBind',
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::BIND => 'bindData',
        );
    }

    /**
     * @param event FormEvent
     */
    public function bindData(FormEvent $event)
    {
        $data               = $event->getData();
        $boat               = $data->getBoat();
        $charter            = $boat->getCharter();
        $reservationRange   = $data->getReservationRange();
        $from               = $reservationRange->getReservationFrom();
        $to                 = $reservationRange->getReservationTo();
        
        $em                 = $this->container->get('doctrine.orm.entity_manager');
        $reservationRepo    = $em->getRepository('ZizooReservationBundle:Reservation');
        
        $overlapRequestedReservations   = $reservationRepo->getReservations($charter, null, $boat, $from, $to, array(Reservation::STATUS_REQUESTED));
        $overlapExternalReservations    = $reservationRepo->getReservations($charter, null, $boat, $from, $to, array(Reservation::STATUS_SELF));
        
        $data->setOverlappingExternalReservations($overlapExternalReservations);
        $data->setOverlappingReservationRequests($overlapRequestedReservations);
        
        $form = $event->getForm();
        
        if (count($overlapRequestedReservations)>0){
            $form->add('deny_reservation', new DenyReservationType(), array('label' => ' '));
        }
        
    }
    
    /**
     * @param event FormEvent
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        
        // Before binding the form, the "meetup" will be null
        if (null === $data) {
            return;
        }


//        $reservationRange = $event->getData()->getReservationRange();
//
//        $form = $event->getForm();
//        $positions = $meetup->getSport()->getPositions();
//
//        $this->customizeForm($form, $positions);
    }

    public function preBind(FormEvent $event)
    {
//        $data = $event->getData();
//        $id = $data['event'];
//        $meetup = $this->em
//            ->getRepository('AcmeDemoBundle:SportMeetup')
//            ->find($id);
//
//        if ($meetup === null) {
//            $msg = 'The event %s could not be found for you registration';
//            throw new \Exception(sprintf($msg, $id));
//        }
//        $form = $event->getForm();
//        $positions = $meetup->getSport()->getPositions();
//
//        $this->customizeForm($form, $positions);
        $data = $event->getData();
        
        // Before binding the form, the "meetup" will be null
        if (null === $data) {
            return;
        }

        if (array_key_exists('reservation_range', $data)){
            $reservation_range = $data['reservation_range'];
            if (array_key_exists('reservation_to', $reservation_range) && array_key_exists('reservation_from', $reservation_range)){
                $from   = $reservation_range['reservation_from'];
                $to     = $reservation_range['reservation_to'];
                
            }
        }

//        $reservationRange = $event->getData()->getReservationRange();
//
//        $form = $event->getForm();
//        $positions = $meetup->getSport()->getPositions();
//
//        $this->customizeForm($form, $positions);
    }

    protected function customizeForm($form, $positions)
    {
        // ... customize the form according to the positions
    }
    
}

?>
