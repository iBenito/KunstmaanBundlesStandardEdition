<?php
namespace Zizoo\BoatBundle\Service;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Entity\BoatType;
use Zizoo\BoatBundle\Entity\Price;
use Zizoo\BoatBundle\Entity\Image;

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
    
    public function addPrice(Boat $boat, Price $price, $flush=true){
        $boat->addPrice($price);
        $price->setBoat($boat);
        $this->em->persist($price);
        if ($flush){
            $this->em->flush();
        }
    }
    
    public function createBoat(Boat $boat, BoatAddress $address, BoatType $boatType, ArrayCollection $prices=null){

        $boat->setBoatType($boatType);
        
        $address->fetchGeo();
        $address->setBoat($boat);
        $boat->setAddress($address);
        if ($prices){
            foreach ($prices as $price){
                $this->addPrice($boat, $price, false);
            }
        }
        
        $this->em->persist($address);
        $this->em->persist($boat);
        $this->em->flush();
        return $boat;
    }
    
    public function createBoatType($name, $order){
        $boatType = new BoatType($name, $order);
        $this->em->persist($boatType);
        $this->em->flush($boatType);
        return $boatType;
    }
    
}
?>
