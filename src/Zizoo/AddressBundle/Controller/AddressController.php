<?php

namespace Zizoo\AddressBundle\Controller;

use Zizoo\BoatBundle\Entity\Boat;
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
            $sub_locality   = $location['subLocality'];
            $state          = $location['state'];
            $province       = $location['province'];
            if (!array_key_exists($countryName, $groupedLocations)){
                $groupedLocations[$countryName] = array('name' => $countryName, 'locations' => array());
            }
            if ($locality && !array_key_exists($locality, $groupedLocations[$countryName]['locations'])){
                $groupedLocations[$countryName]['locations'][$locality] = $locality;
            }
            if ($sub_locality && !array_key_exists($sub_locality, $groupedLocations[$countryName]['locations'])){
                $groupedLocations[$countryName]['locations'][$sub_locality] = $sub_locality;
            } 
            if ($state && !array_key_exists($state, $groupedLocations[$countryName]['locations'])){
                $groupedLocations[$countryName]['locations'][$state] = $state;
            }
            if ($province && !array_key_exists($province, $groupedLocations[$countryName]['locations'])){
                $groupedLocations[$countryName]['locations'][$province] = $province;
            }
        }

        foreach ($groupedLocations as $country => $groupedLocation){
            ksort($groupedLocations[$country]['locations']);
        }
        
        //$response = new Response(json_encode($groupedLocations));
        //return $response;
        return $this->render('ZizooAddressBundle:Address:unique_locations.html.twig', array(
            'unique_locations' => $groupedLocations
        ));
    }
    

    public function locationMarkersAction($search){
        $em = $this->getDoctrine()
                   ->getEntityManager();
        $markers = array();
        if ($search!='-1'){
            $addresses = $em->getRepository('ZizooAddressBundle:BoatAddress')->search($search);
            foreach ($addresses as $address){
                $markers[] = array('name' => $address->getBoat()->getName(), 'location' => array('lat' => $address->getLat(), 'lng' => $address->getLng()));
            }
        } else {
            $boats = $em->getRepository('ZizooBoatBundle:Boat')->findAll();
            foreach ($boats as $boat){
                $addresses = $boat->getAddresses();
                foreach ($addresses as $address){
                    $markers[] = array('name' => $boat->getName(), 'location' => array('lat' => $address->getLat(), 'lng' => $address->getLng()));
                }
            }
        }
        
        
        
        
        $response = new Response(json_encode($markers));
        return $response;
        
        return $this->render('ZizooAddressBundle:Address:location_markers.html.twig', array(
            'boats' => $boats
        ));
    }
    
    
    public function locationsAction(){
        $em = $this->getDoctrine()
                   ->getEntityManager();
        
        $boats = $em->getRepository('ZizooBoatBundle:Boat')->findAll();
        
        return $this->render('ZizooAddressBundle:Address:locations.html.twig', array(
            'boats' => $boats
        ));
    }
    
}
