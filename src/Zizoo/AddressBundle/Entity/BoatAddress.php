<?php
namespace Zizoo\AddressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Zizoo\AddressBundle\Entity\BoatAddressRepository")
 */
class BoatAddress extends AddressBase {
    
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\BoatBundle\Entity\Boat", inversedBy="addresses")
     */
    protected $boat;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lat;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lng;

    /**
     * Set boat
     *
     * @param \Zizoo\BoatBundle\Entity\Boat $boat
     * @return BoatAddress
     */
    public function setBoat(\Zizoo\BoatBundle\Entity\Boat $boat = null)
    {
        $this->boat = $boat;
    
        return $this;
    }

    /**
     * Get boat
     *
     * @return \Zizoo\BoatBundle\Entity\Boat 
     */
    public function getBoat()
    {
        return $this->boat;
    }

    /**
     * Set lat
     *
     * @param string $lat
     * @return BoatAddress
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    
        return $this;
    }

    /**
     * Get lat
     *
     * @return string 
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param string $lng
     * @return BoatAddress
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    
        return $this;
    }

    /**
     * Get lng
     *
     * @return string 
     */
    public function getLng()
    {
        return $this->lng;
    }
    
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