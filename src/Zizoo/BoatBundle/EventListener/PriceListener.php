<?php
// src/Zizoo/ReservationBundle/EventListener/ReservationListener.php
namespace Zizoo\BoatBundle\EventListener;

use Zizoo\BoatBundle\Entity\Price;
use Zizoo\BoatBundle\Exception\InvalidPriceException;

use Zizoo\ReservationBundle\Entity\Reservation;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;

class PriceListener
{
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
//    private function handlePriceEntity(Price $price)
//    {
//        $boat               = $price->getBoat();
//        $from               = $price->getAvailableFrom();
//        $until              = $price->getAvailableUntil();
//        $from->setTime(0,0,0);
//        $until->setTime(23,59,59);
//
//        $reservationAgent   = $this->container->get('zizoo_reservation_reservation_agent');
//        $prices             = $reservationAgent->getPrices($boat, $from, $until);
//        $reservation        = $reservationAgent->getReservation($boat, $from, $until);
//        $booking            = null;
//        if ($reservation) $booking = $reservation->getBooking();
//            
//        if ($prices->count())
//        {
//            $priceStr = '';
//            foreach ($prices as $p){
//                $priceStr .= $p->getId() . ': ' . $p->getAvailableFrom()->format('d/m/Y') . ' - ' . $p->getAvailableUntil()->format('d/m/Y') . "\n";
//            }
//            throw new InvalidPriceException('Overlapping prices: ' . $priceStr);
//        } else if ($reservation && $reservation->getStatus()==Reservation::STATUS_ACCEPTED){
//            $reservationStr = $reservation->getId() . ': ' . $reservation->getCheckIn()->format('d/m/Y') . ' - ' . $reservation->getCheckOut()->format('d/m/Y') . "\n";
//            throw new InvalidPriceException('Overlapping booking: ' . $reservationStr);
//        }
//    }
    
//    public function prePersist(LifecycleEventArgs $args)
//    {
//        $entity = $args->getEntity();
//        
//        if ($entity instanceof Price) {
//            $this->handlePriceEntity($entity);
//        }
//    }
    
    public function onFlush(OnFlushEventArgs $args)
    {
        $em     = $args->getEntityManager();
        $uow    = $em->getUnitOfWork();
        
        $instertUpdateEntities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates()
        );
        
        
        $prices = array();
        
        foreach ($instertUpdateEntities as $entity){
            if ($entity instanceof Price) {
                $prices[] = $entity;
            }
        }
        
        $boat    = null;
        $minDate = null;
        $maxDate = null;
        foreach ($prices as $price){
            if ($minDate==null) {
                $boat       = $price->getBoat();
                $minDate    = clone $price->getAvailable();
            } else {
                if ($price->getAvailable() < $minDate) $minDate = clone $price->getAvailable();
            }
            
            if ($maxDate==null) {
                $maxDate = clone $price->getAvailable();
            } else {
                if ($price->getAvailable() > $maxDate) $maxDate = clone $price->getAvailable();
            }
        }
        
        if ($boat){
            $reservationAgent   = $this->container->get('zizoo_reservation_reservation_agent');
            $reservation        = $reservationAgent->getReservation($boat, $minDate, $maxDate);
            if ($reservation){
                if ($reservation->getStatus()==Reservation::STATUS_ACCEPTED || $reservation->getStatus()==Reservation::STATUS_REQUESTED || $reservation->getStatus()==Reservation::STATUS_SELF){
                    $reservationStr = $reservation->getId() . ': ' . $reservation->getCheckIn()->format('d/m/Y') . ' - ' . $reservation->getCheckOut()->format('d/m/Y') . "\n";
                    throw new InvalidPriceException('Overlapping reservation: ' . $reservationStr);
                }
            }
        }
        
    }
    
    
    public function getSubscribedEvents() {
        return array(
            //Events::prePersist,
            Events::onFlush,
        );
    }
}
?>
