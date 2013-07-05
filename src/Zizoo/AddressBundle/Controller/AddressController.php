<?php

namespace Zizoo\AddressBundle\Controller;

use Zizoo\AddressBundle\Form\Model\SearchBoat;
use Zizoo\AddressBundle\Form\Type\SearchBoatType;
use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Form\Type\BoatTypeType;

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
     * Displays the locations page. Allows user to search by location, date range and number of people.
     * Displays results on Google Maps and as list. List is paged on the client-side, since we have to fetch all boats
     * to display on the map according to search criteria anyway.
     * If request is AJAX return only a component of the locations page.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request    Used to check if AJAX request
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function locationsAction(Request $request){       
        $form = $this->createForm(new SearchBoatType($this->container), new SearchBoat(), array('filter' => true));
        
        $form->bind($request);
        $searchBoat = $form->getData();
        
        // hack for location search
        $boatSearch = $request->query->get('zizoo_boat_search', null);
        if ($boatSearch){
            $searchBoat->setLocation($boatSearch['location']);
        } 
        if (!$searchBoat->getPage()){
            // 1 is default for paging
            $searchBoat->setPage(1);
        }
        
        $pageSize   = $request->query->get('page_size', '9');

        $em = $this->getDoctrine()
                   ->getManager();
        
        $minMaxBoatValues   = $em->getRepository('ZizooBoatBundle:Boat')->getMaxBoatValues();
        $minMaxPrice        = $em->getRepository('ZizooBoatBundle:Price')->getMinimumAndMaximumPrice();
        
        $availableBoats = $em->getRepository('ZizooBoatBundle:Boat')->searchBoats($searchBoat);
        $numAvailableBoats = count($availableBoats);
        $numPages = floor($numAvailableBoats / $pageSize);
        if ($numAvailableBoats % $pageSize > 0){
            $numPages++;
        }

        if ($request->isXmlHttpRequest()){
            return $this->render('ZizooAddressBundle:Address:locations_boats.html.twig', array(
                'boats'             => $availableBoats,
                'page'              => $searchBoat->getPage(),
                'page_size'         => $pageSize,
                'num_pages'         => $numPages,                
                'max_length'        => $minMaxBoatValues['max_length'],
                'max_cabins'        => $minMaxBoatValues['max_cabins'],
                'min_price'         => $minMaxBoatValues['min_lowest_price']?$minMaxBoatValues['min_lowest_price']:1,
                'max_price'         => $minMaxBoatValues['max_highest_price']?$minMaxBoatValues['max_highest_price']:10000,
                'form'              => $form->createView()
            ));
        } else {
            return $this->render('ZizooAddressBundle:Address:locations.html.twig', array(
                'boats'             => $availableBoats,
                'page'              => $searchBoat->getPage(),
                'page_size'         => $pageSize,
                'num_pages'         => $numPages,
                'max_length'        => $minMaxBoatValues['max_length'],
                'max_cabins'        => $minMaxBoatValues['max_cabins'],
                'min_price'         => $minMaxBoatValues['min_lowest_price']?$minMaxBoatValues['min_lowest_price']:1,
                'max_price'         => $minMaxBoatValues['max_highest_price']?$minMaxBoatValues['max_highest_price']:10000,
                'form'              => $form->createView()
            ));
        }
        
        
    }
    
    
    public function locations2Action(Request $request){
        $form = $this->createForm(new SearchBoatType($this->container), new SearchBoat());
        
        $form->bind($request);
        $searchBoat = $form->getData();
        
        // hack for location search
        $boatSearch = $request->query->get('zizoo_boat_search', null);
        if ($boatSearch){
            $searchBoat->setLocation($boatSearch['location']);
        } 
        if (!$searchBoat->getPage()){
            $searchBoat->setPage(1);
        }
        
        $pageSize   = $request->query->get('page_size', '9');

        $em = $this->getDoctrine()
                   ->getManager();
        
        $maxBoatValues   = $em->getRepository('ZizooBoatBundle:Boat')->getMaxBoatValues();
        
        $availableBoats = $em->getRepository('ZizooBoatBundle:Boat')->searchBoats($searchBoat);
        $numAvailableBoats = count($availableBoats);
        $numPages = floor($numAvailableBoats / $pageSize);
        if ($numAvailableBoats % $pageSize > 0){
            $numPages++;
        }

        
        $map = $this->get('ivory_google_map.map');
        $map->setAsync(true);
        $map->setAutoZoom(false);
        $boatAddress = $boat->getAddress();
        $map->setCenter($boatAddress->getLat(), $boatAddress->getLng(), true);
        $map->setMapOption('zoom', 4);
        $map->setMapOption('disableDefaultUI', true);
        $map->setMapOption('zoomControl', true);
        $map->setStylesheetOptions(array(
            'width' => '100%',
            'height' => '300px'
        ));
        
        if ($request->isXmlHttpRequest()){
            return $this->render('ZizooAddressBundle:Address:locations_boats.html.twig', array(
                'boats'             => $availableBoats,
                'page'              => $searchBoat->getPage(),
                'page_size'         => $pageSize,
                'num_pages'         => $numPages,                
                'max_length'        => $maxBoatValues['max_length'],
                'max_cabins'        => $maxBoatValues['max_cabins'],
                'form'              => $form->createView(),
                'map'               => $map
            ));
        } else {
            return $this->render('ZizooAddressBundle:Address:locations.html.twig', array(
                'boats'             => $availableBoats,
                'page'              => $searchBoat->getPage(),
                'page_size'         => $pageSize,
                'num_pages'         => $numPages,
                'max_length'        => $maxBoatValues['max_length'],
                'max_cabins'        => $maxBoatValues['max_cabins'],
                'form'              => $form->createView(),
                'map'               => $map
            ));
        }
        
        
    }
    
    public function searchBarAction($filter=false)
    {
        $form = $this->createForm(new SearchBoatType($this->container), new SearchBoat(), array('filter' => $filter));
        
        $a = $form->createView();
        
        if ($filter){
            $em = $this->getDoctrine()
                   ->getManager();
        
            $minMaxBoatValues   = $em->getRepository('ZizooBoatBundle:Boat')->getMaxBoatValues();
            
            return $this->render('ZizooAddressBundle:Address:search_bar.html.twig',array(
                'form' => $form->createView(),
                'max_length'        => $minMaxBoatValues['max_length'],
                'max_cabins'        => $minMaxBoatValues['max_cabins'],
                'min_price'         => $minMaxBoatValues['min_lowest_price']?$minMaxBoatValues['min_lowest_price']:1,
                'max_price'         => $minMaxBoatValues['max_highest_price']?$minMaxBoatValues['max_highest_price']:10000
            ));
        } else {
            return $this->render('ZizooAddressBundle:Address:search_bar.html.twig',array(
                'form' => $form->createView()
            ));
        }
        
    }
    
    public function searchBlockAction()
    {
        $form = $this->createForm(new SearchBoatType($this->container), new SearchBoat());
        
        return $this->render('ZizooAddressBundle:Address:search_block.html.twig',array(
            'form' => $form->createView()
        ));
    }
}
