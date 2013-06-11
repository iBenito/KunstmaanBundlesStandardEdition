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
    
    public function price($bookBoat, $price){
        if (!$price) return '';
        return number_format($price, 2) . ' &euro;';
    }

    public function numberOfDays($bookBoat){
        $reservationRange = $bookBoat->getReservationRange();
        $from   = $reservationRange->getReservationFrom();
        $to     = $reservationRange->getReservationTo();
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
