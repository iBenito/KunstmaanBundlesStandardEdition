<?php
namespace Zizoo\AddressBundle\Service;

use Zizoo\AddressBundle\Entity\AddressBase as Address;


class AddressService {

    /**
     * Gets a formatted boat address, used by fetchGeo() to search.
     * @return string   Formatted address
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    private function getFormattedAddress(Address $address)
            {
        $addressArr = array();
        
        $addressLine1 = $address->getAddressLine1();
        if ($addressLine1 && $addressLine1!=''){
            $addressArr[] = $addressLine1;
        }
        
        $addressLine2 = $address->getAddressLine2();
        if ($addressLine2 && $addressLine2!=''){
            $addressArr[] = $addressLine2;
        }
        
        $postcode = $address->getPostcode();
        if ($postcode && $postcode!=''){
            $addressArr[] = $postcode;
        }
        
        $locality = $address->getLocality();
        if ($locality && $locality!=''){
            $addressArr[] = $locality;
        }
        
        $countryEntity = $address->getCountry();
        if ($countryEntity){
            $country = $countryEntity->getPrintableName();
            if ($country && $country!=''){
                $addressArr[] = $country;
            }
        }
        
        return implode(',', $addressArr);
    }
    
    /**
     * Fetch geo data (lat, lng) for boat address from Google Maps service. Requires CURL.
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function fetchGeo(Address $address){
        try {
            // jSON URL which should be requested
            $json_url = 'http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($this->getFormattedAddress($address)).'&sensor=false';

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

                $address->setLat($geoLocation->lat);
                $address->setLng($geoLocation->lng);
            }
        } catch (\Exception $e){
            // TODO: log
        }
    }
    
}
?>
