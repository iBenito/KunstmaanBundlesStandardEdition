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
            'reservedDates' => new \Twig_Filter_Method($this, 'reservedDates'),
            'priceDates' => new \Twig_Filter_Method($this, 'priceDates'),
            'displayDefaultPrice' => new \Twig_Filter_Method($this, 'displayDefaultPrice'),
        );
    }
    
    public function reservationExists($boat, $from, $to){
        $reservationAgent = $this->container->get('zizoo_reservation_reservation_agent');
        return $reservationAgent->reservationExists($boat, $from, $to);
    }

    public function bookable($boat, $from, $to){
        if (!$from || !$to) return true;
        $reservationAgent = $this->container->get('zizoo_reservation_reservation_agent');
        $prices = $reservationAgent->getPrices($boat, $from, $to);
        return $prices && !$reservationAgent->reservationExists($boat, $from, $to);
    }
    
    public function reservedDates($boat)
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
    
    public function priceDates($boat)
    {
        $arr = array();
        $prices = $boat->getPrice();
        foreach ($prices as $price){
            $from   = clone $price->getAvailableFrom();
            $to     = $price->getAvailableUntil();
            
            do {
                $arr[] = array($from->format('Y'), $from->format('m'), $from->format('d'), number_format($price->getPrice(),2));
                $from = $from->modify('+1 day');
            } while ($from < $to);
        }
        return json_encode($arr);
    }
    
    public function displayDefaultPrice($boat)
    {
        return number_format($boat->getDefaultPrice(), 2);
    }
    
    public function getName()
    {
        return 'boat_extension';
    }
}
?>
