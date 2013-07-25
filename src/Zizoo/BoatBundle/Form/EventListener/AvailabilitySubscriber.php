<?php
namespace Zizoo\BoatBundle\Form\EventListener;

use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\ReservationBundle\Form\Type\DenyReservationType;

use Symfony\Component\DependencyInjection\Container;
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
        
        $from->setTime(12,0,0);
        $to->setTime(11,59,59);
        
        $overlapRequestedReservations   = $reservationRepo->getReservations($charter, null, $boat, $from, $to, array(Reservation::STATUS_REQUESTED));
        $overlapExternalReservations    = $reservationRepo->getReservations($charter, null, $boat, $from, $to, array(Reservation::STATUS_SELF));
        $overlapBookedReservations      = $reservationRepo->getReservations($charter, null, $boat, $from, $to, array(Reservation::STATUS_ACCEPTED, Reservation::STATUS_HOLD));
        
        $data->setOverlappingExternalReservations($overlapExternalReservations);
        $data->setOverlappingReservationRequests($overlapRequestedReservations);
        $data->setOverlappingBookedReservations($overlapBookedReservations);
                
    }
    
    
    /**
     * @param event FormEvent
     */
    public function preSetData(FormEvent $event)
    {
       
    }

    public function preBind(FormEvent $event)
    {
        $em                 = $this->container->get('doctrine.orm.entity_manager');
        $boatRepo           = $em->getRepository('ZizooBoatBundle:Boat');
        $reservationRepo    = $em->getRepository('ZizooReservationBundle:Reservation');
        
        $data = $event->getData();
        $boatId = $data['boat_id'];
        $boat = $boatRepo->findOneById($boatId);
        
        if ($boat === null) {
            $msg = 'The boat %s could not be found';
            throw new \Exception(sprintf($msg, $boatId));
        }
        
        $charter = $boat->getCharter();
        if ($charter === null) {
            $msg = 'The boat %s could not be found';
            throw new \Exception(sprintf($msg, $boatId));
        }
        
        $form = $event->getForm();
        
        if (array_key_exists('reservation_range', $data)){
            $reservationRange = $data['reservation_range'];
            if  (array_key_exists('reservation_from', $reservationRange) && array_key_exists('reservation_to', $reservationRange)){
                $from   = $reservationRange['reservation_from'];
                $to     = $reservationRange['reservation_to'];
                $from = \DateTime::createFromFormat('d/m/Y', $from);
                $to = \DateTime::createFromFormat('d/m/Y', $to);
                if ($from instanceof \DateTime && $to instanceof \DateTime){
                    $from->setTime(12,0,0);
                    $to->setTime(11,59,59);

                    $overlapRequestedReservations   = $reservationRepo->getReservations($charter, null, $boat, $from, $to, array(Reservation::STATUS_REQUESTED));
                    $overlapExternalReservations    = $reservationRepo->getReservations($charter, null, $boat, $from, $to, array(Reservation::STATUS_SELF));
                    $overlapBookedReservations      = $reservationRepo->getReservations($charter, null, $boat, $from, $to, array(Reservation::STATUS_ACCEPTED, Reservation::STATUS_HOLD));
                    
                    if (count($overlapBookedReservations)==0){
                        if (count($overlapRequestedReservations)>0){
                            $form->add('deny_reservation', new DenyReservationType(), array('label' => ' '));
                        }

                        if (count($overlapExternalReservations)>0 || count($overlapRequestedReservations)>0){
                            $form->add('confirm', 'checkbox', array(
                                                    'required'  => true,
                                                    'label'     => 'Confirm'));
                        }
                    }
                }
            }
        }
        

    }

    
    
}

?>
