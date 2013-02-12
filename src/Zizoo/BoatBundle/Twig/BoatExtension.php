<?php

namespace Zizoo\BoatBundle\Twig;

class BoatExtension extends \Twig_Extension
{
    
    public function getFilters()
    {
        return array(
            'reservationExists' => new \Twig_Filter_Method($this, 'reservationExists'),
            'bookable' => new \Twig_Filter_Method($this, 'bookable'),
            'availableDates' => new \Twig_Filter_Method($this, 'availableDates'),
            'bookedDates' => new \Twig_Filter_Method($this, 'bookedDates'),
        );
    }
    
    public function reservationExists($boat, $from, $to){
        if ($from=='' || $to=='') return false;
        $reservations = $boat->getReservation();
        if (count($reservations)==0) return false;
        
        
        
        foreach ($reservations as $reservation){
            $checkIn = $reservation->getCheckIn();
            $checkout = $reservation->getCheckOut();
            $inRange = ($from < $checkout) && ($to > $checkIn);
            //$inRange = !(($from < $checkIn && $to < $checkout) || ($from > $checkIn && $to > $checkout));
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
    
    public function availableDates2($boat)
    {
        $arr = array();
        $availabilities = $boat->getAvailability();
        foreach ($availabilities as $availability){
            $from   = clone $availability->getAvailableFrom();
            $to     = $availability->getAvailableUntil();
            
            do {
                $arr[] = 'new Date('.$from->format('Y,m,d').')';
                $from = $from->modify('+1 day');
            } while ($from <= $to);
            
        }        
        return '[' . implode(",", $arr) . ']';
    }
    
    public function availableDates($boat)
    {
        $arr = array();
        $reservations = $boat->getReservation();
        $availabilities = $boat->getAvailability();
        foreach ($availabilities as $availability){
            $availableFrom  = clone $availability->getAvailableFrom();
            $availableUntil = $availability->getAvailableUntil();
            
            do {
                foreach ($reservations as $reservation){
                    $reservedFrom   = clone $reservation->getCheckIn();
                    $reservedUntil  = $reservation->getCheckOut();

                    if ($availableFrom < $reservedFrom || $availableFrom > $reservedUntil){
                        $arr[] = array($availableFrom->format('Y'), $availableFrom->format('m'), $availableFrom->format('d'));
                        //$arr[] = 'new Date('.$availableFrom->format('Y,m,d').')';
                    } else {
                        $availableFrom = clone $reservedUntil;
                        break;
                    }
                    
                   
                } 
                $availableFrom = $availableFrom->modify('+1 day');
            } while ($availableFrom <= $availableUntil);
            
        }        
        return json_encode($arr);
        //return '[' . implode(",", $arr) . ']';
    }
    
    public function bookedDates($boat)
    {
        $arr = array();
        $reservations = $boat->getReservation();
        foreach ($reservations as $reservation){
            $from   = clone $reservation->getCheckIn();
            $to     = $reservation->getCheckOut();
            
            do {
                $arr[] = array($from->format('Y'), $from->format('m'), $from->format('d'));
                $from = $from->modify('+1 day');
            } while ($from < $to);
        }
        return json_encode($arr);
    }
    
    public function getName()
    {
        return 'boat_extension';
    }
}
?>
