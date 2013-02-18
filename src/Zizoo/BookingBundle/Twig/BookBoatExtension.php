<?php

namespace Zizoo\BookingBundle\Twig;

use Symfony\Component\DependencyInjection\Container;

class BookBoatExtension extends \Twig_Extension
{
    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function getFilters()
    {
        return array(
            'BookBoat_price' => new \Twig_Filter_Method($this, 'price'),
            'BookBoat_numberOfDays' => new \Twig_Filter_Method($this, 'numberOfDays'),
        );
    }
    
    public function price($bookBoat, $boat){
        $from   = $bookBoat->getReservationFrom();
        $to     = $bookBoat->getReservationTo();
        if (!$from || !$to) return '...';
        $reservationAgent = $this->container->get('zizoo_reservation_reservation_agent');
        $totalPrice = $reservationAgent->getTotalPrice($boat, $from, $to);
        return number_format($totalPrice, 2);
    }

    public function numberOfDays($bookBoat){
        $from   = $bookBoat->getReservationFrom();
        $to     = $bookBoat->getReservationTo();
        if (!$from || !$to) return '...';
        $interval = $from->diff($to);
        return $interval->days;
    }
    
    public function getName()
    {
        return 'book_boat_extension';
    }
}
?>
