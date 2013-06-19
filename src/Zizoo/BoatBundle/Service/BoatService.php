<?php
namespace Zizoo\BoatBundle\Service;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Entity\BoatType;
use Zizoo\BoatBundle\Entity\Price;
use Zizoo\BoatBundle\Entity\Image;
use Zizoo\BoatBundle\Entity\Equipment;

use Zizoo\CharterBundle\Entity\Charter;
use Zizoo\AddressBundle\Entity\BoatAddress;

use Doctrine\Common\Collections\ArrayCollection;



class BoatService {
    
    private $em;
    private $messenger;
    private $container;
    
    public function __construct($em, $messenger, $container) {
        $this->em = $em;
        $this->messenger = $messenger;
        $this->container = $container;
    }
    
    public function addPrice(Boat $boat, $from, $to, $p, $default=false, $flush=true){
        $from   = $from->setTime(0,0,0);
        $to     = $to->setTime(0,0,0);
        
        $overlappingPrices = $this->em->getRepository('ZizooBoatBundle:Price')->getPrices($boat, $from, $to);
        //$overlappingPrices = new \Doctrine\Common\Collections\ArrayCollection(array());
        $priceArr = array();
        foreach ($overlappingPrices as $overlappingPrice){
            $overlappingDate = $overlappingPrice->getAvailable();
            $priceArr[$overlappingDate->format('Y')][$overlappingDate->format('m')][$overlappingDate->format('d')] = $overlappingPrice;
        }
        
        $d = new \DateTime();
        
        while ($from <= $to){
            $year   = $from->format('Y');
            $month  = $from->format('m');
            $day    = $from->format('d');
            if (array_key_exists($year, $priceArr) && array_key_exists($month, $priceArr[$year]) && array_key_exists($day, $priceArr[$year][$month])){
                $overlappingPrice = $priceArr[$year][$month][$day];
                if ($default){
                    $overlappingPrice->setBoat(null);
                    $boat->removePrice($overlappingPrice);
                    $this->em->remove($overlappingPrice);
                } else {
                    $overlappingPrice->setPrice($p);
                    $overlappingPrice->setUpdated($d);
                    $this->em->persist($overlappingPrice);
                }
            } else if (!$default){
                
                $price = new Price();
                $price->setAvailable($from);
                $price->setBoat($boat);
                $price->setCreated($d);
                $price->setPrice($p);
                $price->setUpdated($d);

                $boat->addPrice($price);

                $this->em->persist($price);
            }

            $from = clone $from;
            $from = $from->modify('+1 day');
        }
        
        if ($flush) $this->em->flush();
    }

    public function getPrice($boat, $from, $to)
    {
        if (!$from || !$to) return false;
        $from->setTime(0,0,0);
        $to->setTime(23,59,59);
        $prices = $boat->getPrice();
        foreach ($prices as $price){
            $availableFrom  = $price->getAvailableFrom();
            $availableUntil = $price->getAvailableUntil();
            //(StartA <= EndB) and (EndA >= StartB)
            $inRange = ($from < $availableUntil) && ($to > $availableFrom);
            if ($inRange) return $price;
        }
        return null;
    }
    
    public function addEquipment(Boat $boat, Equipment $equipment, $flush=true)
    {
        $boat->addEquipment($equipment);
        $equipment->addBoat($boat);
        $this->em->persist($equipment);
        if ($flush){
            $this->em->flush();
        }
    }
    
    public function addImages(Boat $boat, ArrayCollection $images){
        foreach ($images as $image){
            $this->addImage($boat, $image);
        }
    }
    
    public function addImage(Boat $boat, Image $image, $flush=true){
        
        //check if image already exists otherwise add it to boat
        if(!($boat->getImage()->contains($image))){
            $boat->addImage($image);
            $this->em->persist($image);
            if ($flush){
                $this->em->flush();
            }
        }
        
    }
    
    public function createBoat(Boat $boat, BoatAddress $address, BoatType $boatType, Charter $charter, ArrayCollection $equipment=null, $flush=false){

        $boat->setBoatType($boatType);
        $boat->setCharter($charter);
        $address->fetchGeo();
        $address->setBoat($boat);
        $boat->setAddress($address);
        $charter->addBoat($boat);
        
        if ($equipment){
            foreach ($equipment as $e){
                $this->addEquipment($boat, $e, false);
            }
        }
        
        $this->em->persist($address);
        $this->em->persist($boat);
        if ($flush) $this->em->flush();
        
        return $boat;
    }
    
    public function createBoatType($id, $name, $order){
        $boatType = new BoatType($id, $name, $order);
        $this->em->persist($boatType);
        $this->em->flush($boatType);
        return $boatType;
    }
    
    public function createEquipment($id, $name, $order){
        $equipment = new Equipment($id, $name, $order);
        $this->em->persist($equipment);
        $this->em->flush($equipment);
        return $equipment;
    }
    
    public function canDeleteBoat(Boat $boat)
    {
        
    }
    
    public function deleteBoat(Boat $boat, $delete)
    {
        $now = new \DateTime();
        $boat->setDeleted($delete==true?$now:null);
        $this->em->persist($boat);
        $this->em->flush();
    }
    
}
?>
