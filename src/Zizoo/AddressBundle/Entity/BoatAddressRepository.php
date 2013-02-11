<?php

namespace Zizoo\AddressBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * BoatAddressRepository
 *
 */
class BoatAddressRepository extends EntityRepository
{
    
    /**
     * Get formatted address, used for geocoding an address when the user searches for a location.
     * @param type $location    Array containing locality, subLocality, state, province, countryName
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    private function getFormattedAddress($location){
        $address = array();
        
        $locality = $location['locality'];
        if ($locality && $locality!=''){
            $address[] = $locality;
        }
        
        $subLocality = $location['subLocality'];
        if ($subLocality && $subLocality!=''){
            $address[] = $subLocality;
        }
        
        $state = $location['state'];
        if ($state && $state!=''){
            $address[] = $state;
        }
        
        $province = $location['province'];
        if ($province && $province!=''){
            $address[] = $province;
        }
        
        $country = $location['countryName'];
        if ($country && $country!=''){
            $address[] = $country;
        }
        
        return implode(',', $address);
    }
    
    public function getUniqueLocations(){
        
        $qb = $this->createQueryBuilder('address')
                    ->select('address.locality, address.subLocality, address.state, address.province, country.iso as countryISO, country.printableName as countryName')
                    ->leftJoin('address.country', 'country')
                    ->addOrderBy('country.printableName, address.locality', 'asc');

        $locations = $qb->getQuery()->getResult();
        
        $groupedLocations = array();
        foreach ($locations as $location){
            $countryName    = $location['countryName'];
            $locality       = $location['locality'];
            $subLocality    = $location['subLocality'];
            $state          = $location['state'];
            $province       = $location['province'];
            if (!array_key_exists($countryName, $groupedLocations)){
                $groupedLocations[$countryName] = array('name' => $countryName, 'locations' => array());
            }
            if ($locality && !array_key_exists($locality, $groupedLocations[$countryName]['locations'])){
                $groupedLocations[$countryName]['locations'][$locality] = array('location' => $locality, 'search' => $this->getFormattedAddress($location));
            }
            if ($subLocality && !array_key_exists($subLocality, $groupedLocations[$countryName]['locations'])){
                $groupedLocations[$countryName]['locations'][$subLocality] = array('location' => $subLocality, 'search' => $this->getFormattedAddress($location));
            } 
            if ($state && !array_key_exists($state, $groupedLocations[$countryName]['locations'])){
                $groupedLocations[$countryName]['locations'][$state] = array('location' => $state, 'search' => $this->getFormattedAddress($location));
            }
            if ($province && !array_key_exists($province, $groupedLocations[$countryName]['locations'])){
                $groupedLocations[$countryName]['locations'][$province] = array('location' => $province, 'search' => $this->getFormattedAddress($location));
            }
        }

        foreach ($groupedLocations as $country => $groupedLocation){
            ksort($groupedLocations[$country]['locations']);
        }
        /**
         return $qb->getQuery()
                   ->getResult();*/
        return $groupedLocations;
        
    }
    
    public function search($search)
    {
        $qb = $this->createQueryBuilder('address')
                   ->select('address, boat, country')
                   ->leftJoin('address.boat', 'boat')
                   ->leftJoin('address.country', 'country')
                   ->where('address.locality = :search')
                   ->orWhere('address.subLocality = :search')
                   ->orWhere('address.state = :search')
                   ->orWhere('address.province = :search')
                   ->orWhere('country.printableName = :search')
                   ->setParameter('search', $search);

        return $qb->getQuery()
                  ->getResult();
    }   
    
}
