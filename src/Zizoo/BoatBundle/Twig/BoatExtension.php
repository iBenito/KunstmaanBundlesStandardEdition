<?php

namespace Zizoo\BoatBundle\Twig;

use Symfony\Component\DependencyInjection\Container;

class BoatExtension extends \Twig_Extension
{
    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function getFilters()
    {
        return array(
            'reservationExists' => new \Twig_Filter_Method($this, 'reservationExists'),
            'bookable' => new \Twig_Filter_Method($this, 'bookable'),
            'reservedDatesWithBookings' => new \Twig_Filter_Method($this, 'reservedDatesWithBookings'),
            'reservedDates' => new \Twig_Filter_Method($this, 'reservedDates'),
            'priceDates' => new \Twig_Filter_Method($this, 'priceDates'),
            'displayDefaultPrice' => new \Twig_Filter_Method($this, 'displayDefaultPrice'),
            'displayFromPrice' => new \Twig_Filter_Method($this, 'displayFromPrice'),
            'displayAmenities' => new \Twig_Filter_Method($this, 'displayAmenities'),
            'displayEquipment' => new \Twig_Filter_Method($this, 'displayEquipment'),
        );
    }
    
    public function reservationExists($boat, $from, $to){
        $reservationAgent = $this->container->get('zizoo_reservation_reservation_agent');
        return $reservationAgent->reservationExists($boat, $from, $to);
    }

    public function bookable($boat, $from, $to){
        if (!$from || !$to) return true;
        $reservationAgent = $this->container->get('zizoo_reservation_reservation_agent');
        //$prices = $reservationAgent->getValidPrices($boat, $from, $to);
        return $reservationAgent->available($boat, $from, $to) && !$reservationAgent->reservationExists($boat, $from, $to);
    }
    
    private function dayState(Reservation $reservation, $date)
    {
        //$date->format()
    }
    
//    public function reservedDatesWithBookings($boat, $reservations)
//    {
//        $arr = array();
//        foreach ($reservations as $reservation){
//            $booking = $reservation->getBooking();
//            $from   = clone $reservation->getCheckIn();
//            $to     = clone $reservation->getCheckOut();
//            $from->setTime(0,0,0);
//            $to->setTime(23,59,59);
//            
//            $arr[$from->format('Y')][$from->format('m')][$from->format('d')][] = array(
//                $reservation->getStatus(), 
//                $reservation->getId(), 
//                ($booking!=null?$booking->getId():null),
//                'start'
//                );
//            $from = $from->modify('+1 day');
//            
//            while ($from <= $to){
//                //$arr[$from->format('Y')][$from->format('m')][$from->format('d')] = array($reservation->getStatus(), $reservation->getId(), ($booking!=null?$booking->getId():null));
//                $arr[$from->format('Y')][$from->format('m')][$from->format('d')][] = array(
//                    $reservation->getStatus(), 
//                    $reservation->getId(), 
//                    ($booking!=null?$booking->getId():null),
//                    null
//                );
//                $from = $from->modify('+1 day');
//            } 
//            $from = $from->modify('-1 day');
//            $arr[$from->format('Y')][$from->format('m')][$from->format('d')] = array();
//            $arr[$from->format('Y')][$from->format('m')][$from->format('d')][] = array(
//                $reservation->getStatus(), 
//                $reservation->getId(), 
//                ($booking!=null?$booking->getId():null),
//                'end'
//            );
//            
//
//        }
//        return json_encode($arr);
//    }
    
    public function reservedDatesWithBookings($boat, $reservations)
    {
        $arr = array();
        if ($reservations !== null){
            foreach ($reservations as $reservation){
                $booking = $reservation->getBooking();
                $from   = clone $reservation->getCheckIn();
                $to     = clone $reservation->getCheckOut();
                $from->setTime(12,0,0);
                $to->setTime(11,59,59);

                do {
                    $arr[$from->format('Y')][$from->format('m')][$from->format('d')][$reservation->getStatus()][] = array(
                        $reservation->getStatus(), 
                        $reservation->getId(), 
                        ($booking!=null?$booking->getId():null),
                        null
                    );
                    $from = $from->modify('+1 day');
                } while ($from <= $to);            

            }
        }
        return json_encode($arr);
    }
    
    
    public function reservedDates($boat, $reservations)
    {
        $arr = array();
        if ($reservations !== null){
            foreach ($reservations as $reservation){
                $from   = clone $reservation->getCheckIn();
                $to     = $reservation->getCheckOut();

                do {
                    //$arr[] = array($from->format('Y'), $from->format('m'), $from->format('d'));
                    $arr[$from->format('Y')][$from->format('m')][$from->format('d')] = array($reservation->getStatus(), $reservation->getId());
                    $from = $from->modify('+1 day');
                } while ($from < $to);
            }
        }
        return json_encode($arr);
    }
    
    public function priceDates($boat, $prices)
    {
        $arr = array();
        if ($prices !== null){
            foreach ($prices as $price){
                $available   = clone $price->getAvailable();            
                $arr[$available->format('Y')][$available->format('m')][$available->format('d')] = array(number_format($price->getPrice(),2));
            }
        }
        return json_encode($arr);
    }
    
    public function displayDefaultPrice($boat)
    {
        return number_format($boat->getDefaultPrice(), 2);
    }
    
    public function displayFromPrice($boat)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $minPrice = $em->getRepository('ZizooBoatBundle:Price')->getMinimumPriceOfBoat($boat);
        if ($minPrice){
            return 'from <span class="price">'.number_format($minPrice, 2).' &euro;</span>';
        } else {
            return '';
        }
    }
    
    public function displayAmenities($boat, $amenities)
    {
        $contains = $boat->getAmenities()->contains($amenities);
        return $contains ? 'yes' : '';
    }
    
    public function displayEquipment($boat, $equipment)
    {
        $contains = $boat->getEquipment()->contains($equipment);
        return $contains ? 'yes' : '';
    }
    
    public function getName()
    {
        return 'boat_extension';
    }
}
?>
