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
        
        //$response = new Response(json_encode($groupedLocations));
        //return $response;
        return $this->render('ZizooAddressBundle:Address:unique_locations.html.twig', array(
            'unique_locations' => $groupedLocations,
            'current'          => $current
        ));
    }
    

    public function locationMarkersAction($search){
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $markers = array();
        if ($search!='-1'){
            $addresses = $em->getRepository('ZizooAddressBundle:BoatAddress')->getMarkers($search);
            var_dump($addresses);
            exit();
            $addresses = $em->getRepository('ZizooAddressBundle:BoatAddress')->search($search);
            foreach ($addresses as $address){
                $markers[] = array('boat' => $address->getBoat(), 'location' => array('lat' => $address->getLat(), 'lng' => $address->getLng()));
            }
        } else {
            $boats = $em->getRepository('ZizooBoatBundle:Boat')->findAll();
            foreach ($boats as $boat){
                $addresses = $boat->getAddresses();
                foreach ($addresses as $address){
                    $markers[] = array('boat' => $boat, 'location' => array('lat' => $address->getLat(), 'lng' => $address->getLng()));
                }
            }
        }

        $response = new Response(json_encode($markers));
        return $response;
        
        return $this->render('ZizooAddressBundle:Address:location_markers.html.twig', array(
            'boats' => $boats
        ));
    }
    
    
    public function locationsAction(Request $request){
        $page       = $request->query->get('page', '1');
        $pageSize   = $request->query->get('page_size', '1');
        $search     = $request->query->get('search', '-1');
        $resFrom    = $request->query->get('reservation_from', '');
        $resTo      = $request->query->get('reservation_to', '');
        $numGuests  = $request->query->get('num_guests', '');
        
        $resFrom = \DateTime::createFromFormat('d/m/Y', $resFrom);
        $resTo = \DateTime::createFromFormat('d/m/Y', $resTo);
        
        if ($resFrom && $resTo){
            $resFrom    = $resFrom->format('Y-m-d') . ' 00:00:00';
            $resTo      = $resTo->format('Y-m-d') . ' 23:59:59';
        } else {
            $resFrom    = '';
            $resTo      = '';
        }
        
        $em = $this->getDoctrine()
                   ->getEntityManager();
        
        $boats = $em->getRepository('ZizooBoatBundle:Boat')->getBoatsWithAddressesAndImages($search, $resFrom, $resTo, $numGuests);
        $numBoats = count($boats);
        $numPages = floor($numBoats / $pageSize);
        if ($numBoats % $pageSize > 0){
            $numPages++;
        }
        
        if ($request->isXmlHttpRequest()){
            return $this->render('ZizooAddressBundle:Address:locations_boats.html.twig', array(
                'boats'     => $boats,
                'page'      => $page,
                'page_size' => $pageSize,
                'num_pages' => $numPages,
                'current'   => $search,
                'res_from'  => $resFrom,
                'res_to'  => $resTo,
                'num_guests'  => $numGuests
            ));
        } else {
            return $this->render('ZizooAddressBundle:Address:locations.html.twig', array(
                'boats'     => $boats,
                'page'      => $page,
                'page_size' => $pageSize,
                'num_pages' => $numPages,
                'current'   => $search,
                'res_from'  => $resFrom,
                'res_to'  => $resTo,
                'num_guests'  => $numGuests
            ));
        }
        
        
    }
    
    
}
