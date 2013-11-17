<?php
namespace Zizoo\BoatBundle\Service;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Entity\BoatType;
use Zizoo\BoatBundle\Entity\Price;
use Zizoo\BoatBundle\Entity\BoatImage;
use Zizoo\BoatBundle\Entity\Equipment;
use Zizoo\BoatBundle\Entity\Amenities;
use Zizoo\BoatBundle\Entity\EngineType;

use Zizoo\CharterBundle\Entity\Charter;
use Zizoo\AddressBundle\Entity\BoatAddress;

use Symfony\Component\HttpFoundation\File\UploadedFile;

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
        $from->setTime(12,0,0);
        $to->setTime(11,59,59);
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
        //$equipment->addBoat($boat);
        $this->em->persist($equipment);
        if ($flush){
            $this->em->flush();
        }
    }

    public function addAmenities(Boat $boat, Amenities $amenities, $flush=true)
    {
        $boat->addAmenities($amenities);
        //$amenities->addBoat($boat);
        $this->em->persist($amenities);
        if ($flush){
            $this->em->flush();
        }
    }
    
    public function addImages(Boat $boat, $imageFiles, $flush=true){
        $boatImages = new ArrayCollection();
        foreach ($imageFiles as $imageFile){
            $boatImages->add($this->addImage($boat, $imageFile, false));
        }
        if ($flush) $this->em->flush();
        return $boatImages;
    }
    
    public function addImage(Boat $boat, $imageFile, $flush=true){
        
        if (!$imageFile instanceof UploadedFile){
            throw new \Exception('Unable to upload');
        }

        $image = new BoatImage();
        $image->setFile($imageFile);
        $image->setPath($imageFile->guessExtension());
        $image->setMimeType($imageFile->getMimeType());
        $image->setBoat($boat);
        $boat->addImage($image);
            
        $validator          = $this->container->get('validator');
        $boatErrors         = $validator->validate($boat, 'boat_photos');
        $imageErrors        = $validator->validate($image, 'boat_photos');
        $numBoatErrors      = $boatErrors->count();
        $numImageErrors     = $imageErrors->count();
        
        if ($numBoatErrors==0 && $numImageErrors==0){
            
            $boat->setUpdated(new \DateTime());

            try {
                $this->em->persist($image);
                
                if ($flush) $this->em->flush();
                
                return $image;
                
            } catch (\Exception $e){
                throw new \Exception('Unable to upload: ' . $e->getMessage());
            }
            
        } else {
            
            $errorArr = array();
            for ($i=0; $i<$numBoatErrors; $i++){
                $error = $boatErrors->get($i);
                $msgTemplate = $error->getMessage();
                $errorArr[] = $msgTemplate;
            }
            for ($i=0; $i<$numImageErrors; $i++){
                $error = $imageErrors->get($i);
                $msgTemplate = $error->getMessage();
                $errorArr[] = $msgTemplate;
            }
            
            throw new \Exception(join(',', $errorArr));
            
        }
             
    }
    
    public function createBoat(Boat $boat, BoatAddress $address, BoatType $boatType = null, Charter $charter, ArrayCollection $equipment=null, EngineType $engineType=null, $flush=false){

        $boat->setBoatType($boatType);
        $boat->setCharter($charter);
        $boat->setEngineType($engineType);
        //$address->fetchGeo();
        $address->setBoat($boat);
        $boat->setAddress($address);
        $charter->addBoat($boat);
        
        if ($equipment){
            foreach ($equipment as $e){
                //$this->addEquipment($boat, $e, false);
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
        return true;
    }
    
    public function deleteBoat(Boat $boat, $delete)
    {
        if ($this->canDeleteBoat($boat)){
            $now = new \DateTime();
            $boat->setDeleted($delete==true?$now:null);
            if ($delete==true){
                $boat->setActive(false);
            }
            $this->em->persist($boat);
            $this->em->flush();
        } else {
            throw new \Exception('Boat cannot be deleted');
        }
    }
    
    public function boatCompleteValidate(Boat $boat)
    {
        $errors = array();
        
        if (!$this->container->hasParameter('zizoo_boat.completeness_validation_groups')){
            $validationGroups = 'Default';
        } else {
            $validationGroups = $this->container->getParameter('zizoo_boat.completeness_validation_groups');
        }
        
        $validator = $this->container->get('validator');
        foreach ($validationGroups as $validationGroup){
            $validationGroupErrors = $validator->validate($boat, $validationGroup);
            $numValidationGroupErrors = $validationGroupErrors->count();
            if ($numValidationGroupErrors>0){
                $errors[$validationGroup] = array();
                for ($i=0; $i<$numValidationGroupErrors; $i++){
                    $errors[$validationGroup][] = $validationGroupErrors->get($i)->getMessage();
                }
            }
        }
        
        return $errors;
    }
    
}
?>
