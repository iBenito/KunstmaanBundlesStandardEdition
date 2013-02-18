<?php

namespace Zizoo\ReservationBundle\Twig;

use Zizoo\ReservationBundle\Entity\Reservation;

class ReservationExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'Reservation_numberOfDays'      => new \Twig_Filter_Method($this, 'numberOfDays'),
        );
    }
    
    public function numberOfDays(Reservation $reservation){
        $from   = $reservation->getCheckIn();
        $to     = $reservation->getCheckOut();
        if (!$from || !$to) return '...';
        $interval = $from->diff($to);
        return $interval->days;
    }
    
    public function getName()
    {
        return 'reservation_extension';
    }
}
?>
