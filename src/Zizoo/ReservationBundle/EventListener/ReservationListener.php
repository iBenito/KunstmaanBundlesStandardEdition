<?php
// src/Zizoo/ReservationBundle/EventListener/ReservationListener.php
namespace Zizoo\ReservationBundle\EventListener;

use Zizoo\ReservationBundle\Entity\Reservation;
use Zizoo\ReservationBundle\Exception\InvalidReservationException;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Event\LifecycleEventArgs;
//use Doctrine\ORM\Event\PreUpdateEventArgs;
//use Doctrine\ORM\Event\OnFlushEventArgs;

class ReservationListener
{
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    private function handleReservationEntity(Reservation $reservation)
    {
        $boat               = $reservation->getBoat();
        $from               = $reservation->getCheckIn();
        $until              = $reservation->getCheckOut();
        $from->setTime(0,0,0);
        $until->setTime(23,59,59);

        $reservationAgent   = $this->container->get('zizoo_reservation_reservation_agent');

        if ($reservationAgent->reservationExists($boat, $from, $until) || !$reservationAgent->available($boat, $from, $until))
        {
            throw new InvalidReservationException('Boat not available for '.$from->format('d/m/Y') . ' - ' . $until->format('d/m/Y'));
        } else if ($reservation->getNrGuests() > $boat->getNrGuests())
        {
            throw new InvalidReservationException('Too many guests: '.$reservation->getNrGuests().'>'.$boat->getNrGuests());
        }
    }
    
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        
        if ($entity instanceof Reservation) {
            $this->handleReservationEntity($entity);
        }
    }
    
    
//    public function onFlush(OnFlushEventArgs $args)
//    {
//        $em     = $args->getManager();
//        $uow    = $em->getUnitOfWork();
//        
//        foreach ($uow->getScheduledEntityInsertions() AS $entity) {
//            if ($entity instanceof Reservation) {
//                $this->handleReservationEntity($entity);
//            }
//        }
//    }
    
    
//    public function postPersist(LifecycleEventArgs $args)
//    {
//        $entity = $args->getEntity();
//        
//        if ($entity instanceof Reservation) {
//            $reservation = $entity;
//            $booking = $reservation->getBooking();
//            if ($booking){
//                $reservationAgent = $this->container->get('zizoo_reservation_reservation_agent');
//                $reservationAgent->sendReservationMessage($booking->getRenter(), $reservation);
//            }
//        }
//    }
    
    public function getSubscribedEvents() {
        return array(
            Events::prePersist,
//            Events::preUpdate,
//            Events::onFlush,
//            Events::postPersist,
        );
    }
}
?>
