<?php

namespace Zizoo\BoatBundle\Twig;


class BoatExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'reservationExists' => new \Twig_Filter_Method($this, 'reservationExists'),
        );
    }

    public function reservationExists($boat, $from, $to){
        if ($from=='' || $to=='') return false;
        $reservations = $boat->getReservation();
        if (count($reservations)==0) return false;
        
        $from = \DateTime::createFromFormat('d/m/Y', $from);
        $to = \DateTime::createFromFormat('d/m/Y', $to);
        
        $from->setTime(0, 0, 0);
        $to->setTime(23, 59, 59);
        
        foreach ($reservations as $reservation){
            $checkIn = $reservation->getCheckIn();
            $checkout = $reservation->getCheckOut();
            $inRange = !(($from < $checkIn && $to < $checkout) || ($from > $checkIn && $to > $checkout));
            if ($inRange) return true;
        }
        return false;
    }

    public function getName()
    {
        return 'boat_extension';
    }
}
?>
