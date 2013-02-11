<?php

namespace Zizoo\BookingBundle\Twig;


class BookBoatExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'BookBoat_price' => new \Twig_Filter_Method($this, 'price'),
            'BookBoat_numberOfDays' => new \Twig_Filter_Method($this, 'numberOfDays'),
        );
    }
    
    public function price($bookBoat, $availability){
        $from   = $bookBoat->getReservationFrom();
        $to     = $bookBoat->getReservationTo();
        if (!$from || !$to) return '...';
        $interval = $from->diff($to);
        $price = $availability->getPrice() * $interval->d;
        return number_format($price, 2);
    }

    public function numberOfDays($bookBoat){
        $from   = $bookBoat->getReservationFrom();
        $to     = $bookBoat->getReservationTo();
        if (!$from || !$to) return '...';
        $interval = $from->diff($to);
        return $interval->d;
    }
    
    public function getName()
    {
        return 'book_boat_extension';
    }
}
?>
