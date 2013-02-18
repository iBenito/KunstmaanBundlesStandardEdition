<?php
namespace Zizoo\AddressBundle\Entity;

use Zizoo\BoatBundle\Entity\Boat;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ReservationAddress extends AddressBase {
    
    /**
     * @ORM\OneToOne(targetEntity="Zizoo\ReservationBundle\Entity\Reservation", inversedBy="address")
     */
    protected $reservation;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lat;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lng;

    
    protected $geoHash;
    
    
    public function __construct(Boat $boat=null) {
        if ($boat){
            parent::__construct($boat->getAddress());
        }
    }
    
    /**
     * Set reservation
     *
     * @param \Zizoo\ReservationBundle\Entity\Reservation $reservation
     * @return ReservationAddress
     */
    public function setReservation(\Zizoo\ReservationBundle\Entity\Reservation $reservation = null)
    {
        $this->reservation = $reservation;
    
        return $this;
    }

    /**
     * Get reservation
     *
     * @return \Zizoo\ReservationBundle\Entity\Reservation 
     */
    public function getReservation()
    {
        return $this->reservation;
    }
    
    /**
     * Gets a formatted boat address, used by fetchGeo() to search.
     * @return string   Formatted address
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function getFormattedAddress(){
        $address = array();
        $street     = $this->getStreet();
        $premise    = $this->getPremise();
        if ($street && $street!=''){
            if ($premise && $premise!=''){
                $address[] = $street . ' ' . $premise;
            } else {
                $address[] = $street;
            }
        }
        
        $postcode = $this->getPostcode();
        if ($postcode && $postcode!=''){
            $address[] = $postcode;
        }
        
        $locality = $this->getLocality();
        if ($locality && $locality!=''){
            $address[] = $locality;
        }
        
        $country = $this->getCountry()->getPrintableName();
        if ($country && $country!=''){
            $address[] = $country;
        }
        
        return implode(',', $address);
    }
    
    /**
     * Fetch geo data (lat, lng) for boat address from Google Maps service. Requires CURL.
     * TODO: Fail gracefully.
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function fetchGeo(){
        // jSON URL which should be requested
        $json_url = 'http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($this->getFormattedAddress()).'&sensor=false';

        // Initializing curl
        $ch = curl_init( $json_url );

        // Configuring curl options
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Content-type: application/json')
        );

        // Setting curl options
        curl_setopt_array( $ch, $options );

        // Getting results
        $result = json_decode(curl_exec($ch)); 

        if ($result->results && count($result->results)>0){
            $geoLocation = $result->results[0]->geometry->location;

            $this->setLat($geoLocation->lat);
            $this->setLng($geoLocation->lng);
           
        }
    }
            
}