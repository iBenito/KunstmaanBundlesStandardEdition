<?php

namespace Zizoo\ReservationBundle\Twig;

use Zizoo\ReservationBundle\Entity\Reservation;

use Symfony\Component\DependencyInjection\Container;

class ReservationExtension extends \Twig_Extension
{
    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function getFilters()
    {
        return array(
            'Reservation_numberOfDays'          => new \Twig_Filter_Method($this, 'numberOfDays'),
            'Reservation_statusToString'        => new \Twig_Filter_Method($this, 'statusToString'),
        );
    }
    
    public function numberOfDays(Reservation $reservation){
        $from   = clone $reservation->getCheckIn();
        $to     = clone $reservation->getCheckOut();
        $from->setTime(0,0,0);
        $to->setTime(0,0,0);
        if (!$from || !$to) return '...';
        $interval = $from->diff($to);
        return $interval->days;
    }
    
    public function statusToString(Reservation $reservation)
    {
        $reservationAgent = $this->container->get('zizoo_reservation_reservation_agent');
        return $reservationAgent->statusToString($reservation->getStatus());
    }
    
    public function getName()
    {
        return 'reservation_extension';
    }
}
?>
