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
       
    
    public function onFlush(OnFlushEventArgs $args)
    {
        $em     = $args->getEntityManager();
        $uow    = $em->getUnitOfWork();
        
        $insertUpdateEntities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates()
        );
        
        
        $prices = array();
        
        foreach ($insertUpdateEntities as $entity){
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
            $boat->updateLowestAndHighestPrice();
            $uow->computeChangeSets();
        }
        
    }
    
    
    public function getSubscribedEvents() {
        return array(
            Events::onFlush
        );
    }
}
?>
