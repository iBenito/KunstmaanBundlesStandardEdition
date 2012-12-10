<?php

namespace Zizoo\AddressBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AddressController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ZizooAddressBundle:Address:index.html.twig', array('name' => $name));
    }
    
    public function uniqueLocationsAction(){
        $em = $this->getDoctrine()
                   ->getEntityManager();
        
        $locations = $em->getRepository('ZizooAddressBundle:BoatAddress')->getUniqueLocations();
        
        $groupedLocations = array();
        foreach ($locations as $location){
            $countryKey     = $location['countryISO'];
            $countryName    = $location['countryName'];
            $locality       = $location['locality'];
            $sub_locality   = $location['sub_locality'];
            $state          = $location['state'];
            $province       = $location['province'];
            if (!array_key_exists($countryKey, $groupedLocations)){
                $groupedLocations[$countryKey] = array('name' => $countryName, 'locations' => array());
            }
            if ($locality && !array_key_exists($locality, $groupedLocations[$countryKey])){
                $groupedLocations[$countryKey]['locations']['locality'] = $locality;
            }
            if ($sub_locality && !array_key_exists($sub_locality, $groupedLocations[$countryKey])){
                $groupedLocations[$countryKey]['locations']['sub_locality'] = $sub_locality;
            }
            if ($state && !array_key_exists($state, $groupedLocations[$countryKey])){
                $groupedLocations[$countryKey]['locations']['state'] = $state;
            }
            if ($province && !array_key_exists($province, $groupedLocations[$countryKey])){
                $groupedLocations[$countryKey]['locations']['province'] = $province;
            }
        }
        
        foreach ($groupedLocations as $country => $groupedLocation){
            ksort($groupedLocations[$country]);
        }
        
        //$response = new Response(json_encode($groupedLocations));
        //return $response;
        return $this->render('ZizooAddressBundle:Address:unique_locations.html.twig', array(
            'unique_locations' => $groupedLocations
        ));
    }
    

    public function locationsMapAction(){
        
        $em = $this->getDoctrine()
                   ->getEntityManager();
        
        $boats = $em->getRepository('ZizooBoatBundle:Boat')->findAll();
        
        return $this->render('ZizooAddressBundle:Address:locations_map.html.twig', array(
            'boats' => $boats
        ));
    }
    
    
    public function locationsTestAction(){
        return $this->render('ZizooAddressBundle:Address:locations_test.html.twig', array());
    }
    
}
