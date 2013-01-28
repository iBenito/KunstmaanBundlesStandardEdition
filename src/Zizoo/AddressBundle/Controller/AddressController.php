<?php

namespace Zizoo\AddressBundle\Controller;

use Zizoo\BoatBundle\Entity\Boat;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddressController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ZizooAddressBundle:Address:index.html.twig', array('name' => $name));
    }
    
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
    
    /**
     * Show all unique locations (country, locality, sub locality, state, province) in a select dropdown.
     * 
     * @param type $current Currently selected location
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function uniqueLocationsAction($current){
        $em = $this->getDoctrine()
                   ->getEntityManager();
        
        $locations = $em->getRepository('ZizooAddressBundle:BoatAddress')->getUniqueLocations();
        
        
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

        return $this->render('ZizooAddressBundle:Address:unique_locations.html.twig', array(
            'unique_locations' => $groupedLocations,
            'current'          => $current
        ));
    }
        
    
    /**
     * Displays the locations page. Allows user to search by location, date range and number of people.
     * Displays results on Google Maps and as list. List is paged on the client-side, since we have to fetch all boats
     * to display on the map according to search criteria anyway.
     * If request is AJAX return only a component of the locations page.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request    Used to check if AJAX request
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function locationsAction(Request $request){
        $page       = $request->query->get('page', '1');
        $pageSize   = $request->query->get('page_size', '9');
        $search     = $request->query->get('search', '-1');
        $resFrom    = $request->query->get('reservation_from', '');
        $resTo      = $request->query->get('reservation_to', '');
        $numGuests  = $request->query->get('num_guests', '');
        
        // Filter
        $numCabinsFrom  = $request->query->get('num_cabins_from', '');
        $numCabinsTo    = $request->query->get('num_cabins_to', '');
        $lengthFrom     = $request->query->get('length_from', '');
        $lengthTo       = $request->query->get('length_to', '');
        
        $em = $this->getDoctrine()
                   ->getEntityManager();
        
        $maxBoatValues   = $em->getRepository('ZizooBoatBundle:Boat')->getMaxBoatValues();
        
        $availableBoats = $em->getRepository('ZizooBoatBundle:Boat')->searchBoatAvailability($search, $numGuests, 
                                                                                            $numCabinsFrom, $numCabinsTo,
                                                                                            $lengthFrom, $lengthTo);
        $numAvailableBoats = count($availableBoats);
        $numPages = floor($numAvailableBoats / $pageSize);
        if ($numAvailableBoats % $pageSize > 0){
            $numPages++;
        }
        
        if ($request->isXmlHttpRequest()){
            return $this->render('ZizooAddressBundle:Address:locations_boats.html.twig', array(
                'boats'         => $availableBoats,
                'page'          => $page,
                'page_size'     => $pageSize,
                'num_pages'     => $numPages,
                'current'       => $search,
                'res_from'      => $resFrom,
                'res_to'        => $resTo,
                'num_guests'    => $numGuests,
                'max_length'    => $maxBoatValues['max_length'],
                'max_cabins'    => $maxBoatValues['max_cabins']
            ));
        } else {
            return $this->render('ZizooAddressBundle:Address:locations.html.twig', array(
                'boats'         => $availableBoats,
                'page'          => $page,
                'page_size'     => $pageSize,
                'num_pages'     => $numPages,
                'current'       => $search,
                'res_from'      => $resFrom,
                'res_to'        => $resTo,
                'num_guests'    => $numGuests,
                'max_length'    => $maxBoatValues['max_length'],
                'max_cabins'    => $maxBoatValues['max_cabins']
            ));
        }
        
        
    }
    
    
}
