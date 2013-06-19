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
        $interval = $from->diff($until);
        $now = new \DateTime();
        $now->setTime(0,0,0);
        $interval2 = $now->diff($from, false);
        
        $reservationAgent   = $this->container->get('zizoo_reservation_reservation_agent');
        $boat               = $reservation->getBoat();
        
        // Ensure that boat is active and not deleted
        if (!$boat->getActive() || $boat->getDeleted()){
            throw new InvalidReservationException('Boat not available');
        } 
        
        // Ensure that the boat is available for desired dates
        if ($reservationAgent->reservationExists($boat, $from, $until) || !$reservationAgent->available($boat, $from, $until)){
            throw new InvalidReservationException('Boat not available for '.$from->format('d/m/Y') . ' - ' . $until->format('d/m/Y'));
        } 
        
        // Ensure that the boat is booked for the specified minimum number of days
        if ($boat->getMinimumDays() && $interval->days < $boat->getMinimumDays() && $reservation->getStatus()!=Reservation::STATUS_SELF){
            throw new InvalidReservationException('Boat must be booked for a minimum of '.$boat->getMinimumDays().' days.');
        }
        
        // Ensure that the booking is in the future
        if ($interval2->invert && $reservation->getStatus()!=Reservation::STATUS_SELF){
            throw new InvalidReservationException('Boat cannot be booked in the past');
        }
        
        // Ensure that the booking is at least N days in the future, where N is a parameter of ReservationBundle (only if reservation status is not self)
        $minDaysInFuture = $this->container->getParameter('zizoo_reservation.min_reservation_days_in_advance');
        if ($interval2->days < $minDaysInFuture && $reservation->getStatus()!=Reservation::STATUS_SELF){
            throw new InvalidReservationException('Boat must be booked at least '.$minDaysInFuture.' days in adance.');
        }
        
        // Ensure that the boat can handle the desired number of guests
        if ($reservation->getNrGuests() > $boat->getNrGuests()){
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
