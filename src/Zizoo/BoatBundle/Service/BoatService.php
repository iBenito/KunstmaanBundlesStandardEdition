<?php
namespace Zizoo\BoatBundle\Service;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Entity\BoatType;
use Zizoo\BoatBundle\Entity\Availability;
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
    
    public function addAvailability(Boat $boat, Availability $availability, $flush=true){
        $boat->addAvailability($availability);
        $availability->setBoat($boat);
        $availabilityAddress = $availability->getAddress();
        $availabilityAddress->fetchGeo();
        $this->em->persist($availabilityAddress);
        $this->em->persist($availability);
        if ($flush){
            $this->em->flush();
        }
    }
    
    public function createBoat($name, $title, $description, $brand, $model, $length, $cabins, $numGuests, BoatAddress $address, BoatType $boatType, ArrayCollection $availabilities=null){
        $boat = new Boat();
        $boat->setName($name);
        $boat->setTitle($title);
        $boat->setDescription($description);
        $boat->setBrand($brand);
        $boat->setModel($model);
        $boat->setLength($length);
        $boat->setCabins($cabins);
        $boat->setNrGuests($numGuests);
        $boat->setBoatType($boatType);
        
        $address->fetchGeo();
        $address->setBoat($boat);
        $boat->setAddress($address);
        if ($availabilities){
            foreach ($availabilities as $availability){
                $this->addAvailability($boat, $availability, false);
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
