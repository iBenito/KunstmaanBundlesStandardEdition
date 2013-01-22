<?php

namespace Zizoo\BoatBundle\Twig;


class BoatExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'reservationExists' => new \Twig_Filter_Method($this, 'reservationExists'),
            'bookable' => new \Twig_Filter_Method($this, 'bookable'),
        );
    }
    
    

    public function reservationExists($boat, $from, $to){
        if ($from=='' || $to=='') return false;
        $reservations = $boat->getReservation();
        if (count($reservations)==0) return false;
        
        
        
        foreach ($reservations as $reservation){
            $checkIn = $reservation->getCheckIn();
            $checkout = $reservation->getCheckOut();
            $inRange = !(($from < $checkIn && $to < $checkout) || ($from > $checkIn && $to > $checkout));
            if ($inRange) return true;
        }
        return false;
    }

    public function bookable($boat, $from, $to){
        $availabilities = $boat->getAvailability();
        if ($availabilities->count()==0) return false;
        if ($from=='' || $to=='') return true;
        foreach ($availabilities as $availability){
            $fromDate = \DateTime::createFromFormat('d/m/Y', $from);
            $toDate = \DateTime::createFromFormat('d/m/Y', $to);

            $fromDate->setTime(0, 0, 0);
            $toDate->setTime(23, 59, 59);
            if ($fromDate >= $availability->getAvailableFrom() 
                    && $toDate <= $availability->getAvailableUntil()
                    && !$this->reservationExists($boat, $fromDate, $toDate))
            {
                return true;
            }
        }
        return false;
    }
    
    public function getName()
    {
        return 'boat_extension';
    }
}
?>
