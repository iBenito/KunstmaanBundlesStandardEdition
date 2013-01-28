<?php

namespace Zizoo\AddressBundle\Twig;


class AvailabilityAddressExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'resetDuplicates' => new \Twig_Filter_Method($this, 'resetDuplicates'),
            'duplicateLocation' => new \Twig_Filter_Method($this, 'duplicateLocation'),
            'duplicateCity' => new \Twig_Filter_Method($this, 'duplicateCity')
        );
    }
    
    protected $geoLocations;
    protected $cities;
       
    public function __construct() {
        $this->geoLocations = array();
        $this->cities       = array();
    }
    
    public function resetDuplicates(){
        $this->geoLocations = array();
        $this->cities = array();
    }
    
    public function duplicateLocation($address){
        $lat = $address->getLat();
        $lng = $address->getLng();
        if ($lat==null || $lng==null) return true;
        $hash = md5($lat.';'.$lng);
        if (array_key_exists($hash, $this->geoLocations)) return true;
        $this->geoLocations[$hash] = '';
        return false;
    }
    
    public function duplicateCity($address){
        $city = $address->getLocality();
        if (array_key_exists($city, $this->cities)) return true;
        $this->cities[$city] = '';
        return false;
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
        return 'availability_address_extension';
    }
}
?>
