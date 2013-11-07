<?php
namespace Zizoo\AycBundle\Command;

use Zizoo\UserBundle\Entity\User;
use Zizoo\UserBundle\Entity\Group;
use Zizoo\AddressBundle\Entity\Country;
use Zizoo\AddressBundle\Entity\Marina;
use Zizoo\AddressBundle\Entity\Language;
use Zizoo\AddressBundle\Entity\ProfileAddress;
use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\ProfileBundle\Entity\Profile\NotificationSettings;
use Zizoo\MessageBundle\Entity\MessageType;
use Zizoo\BoatBundle\Entity\Amenities;
use Zizoo\BoatBundle\Entity\Equipment;
use Zizoo\BoatBundle\Entity\Extra;
use Zizoo\BoatBundle\Entity\BoatType;
use Zizoo\BoatBundle\Entity\EngineType;
use Zizoo\CrewBundle\Entity\SkillType;
use Zizoo\BookingBundle\Entity\PaymentMethod;
use Zizoo\BookingBundle\Entity\InstalmentOption;
use Zizoo\BillingBundle\Entity\PayoutMethod;
use Zizoo\CharterBundle\Entity\Charter;
use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\AddressBundle\Entity\BoatAddress;
use Zizoo\AddressBundle\Entity\CharterAddress;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

use Doctrine\Common\Collections\ArrayCollection;

class LoadFleetCommand extends ContainerAwareCommand
{
    private $container, $em, $boatTypeRepo, $countryRepo;
    private $amenities, $extras, $equipment;
    private $boatService, $charterService;

    private $charter;

    protected function configure()
    {
        $this
            ->setName('ayc:loadfleet')
            ->setDescription('Load AYC fleet into Zizoo');
    }

    private function loadCharter() {

        $userRepo = $this->em->getRepository('ZizooUserBundle:User');
        $user = $userRepo->loadUserByUsername('zizoo_info');
        
        $this->charter = new Charter();
        $this->charter->setCharterName('AYC');

        $this->charter->setAdminUser($user);
        $this->charter->setBillingUser($user);
        $this->charter->addUser($user);
        $user->setCharter($this->charter);

        $charterAddress = new CharterAddress();
        $charterAddress->setAddressLine1('Tiefer Graben 7');
        $charterAddress->setLocality('Vienna');
        $charterAddress->setPostcode('1010');
        $charterAddress->setCountry($this->countryRepo->findOneByIso('AT'));
        $charterAddress->setCharter($this->charter);
        $this->charter->setAddress($charterAddress);
        
        $this->charter = $this->em->persist($this->charter);
        $this->em->persist($charterAddress);
        
    }
    
    private function copyImage($filename, $tempDir)
    {
        $toFile = tempnam($tempDir, $filename); 
        echo "copy ". $filename . " to " . $toFile . "\n";
        copy($filename, $toFile);
        return $toFile;
    }
    
    private function addImage(Boat $boat, $filename)
    {
        $tempDir = ini_get('upload_tmp_dir');
        $tmpFilename = $this->copyImage($filename, $tempDir);
        $uploadedFile = new UploadedFile($tmpFilename, $filename, null, null, null, true);
        $this->boatService->addImage($boat, $uploadedFile, false);
    }

    private function initAssets($repo) {

        $equipmentRepo  = $this->em->getRepository($repo);
        $assets = $equipmentRepo->findAll();

        $hash = array();
        foreach($assets as $asset) {
            $id = $asset->getId();
            $hash[$id] = $asset;
        }

        return $hash;
    }

    private function getAddresses() {

        // Setup the two marina locations
        $addresses = array();

        $addresses["vodice"] = new BoatAddress();
        $addresses["vodice"]->setAddressLine1('Vrulje');
        $addresses["vodice"]->setLocality('Vodice');
        $addresses["vodice"]->setPostcode('22211');
        $addresses["vodice"]->setCountry($this->countryRepo->findOneByIso('HR'));
        
        $addresses["krvavica"] = new BoatAddress();
        $addresses["krvavica"]->setAddressLine1('Krvavica');
        $addresses["krvavica"]->setLocality('Baška Voda');
        $addresses["krvavica"]->setPostcode('21320');
        $addresses["krvavica"]->setCountry($this->countryRepo->findOneByIso('HR'));

        return $addresses;
    }

    private function setBoatPrices($boat, $csv) 
    {

        $dates = array(
            '_29_03__19_04' => DateTime('04/19/2014'),
            '_19_04__03_05' => DateTime('05/03/2014'),
            '_03_05__17_05' => DateTime('05/17/2014'),
            '_17_05__31_05' => DateTime('05/31/2014'),
            '_31_05__14_06' => DateTime('06/14/2014'),
            '_14_06__19_07' => DateTime('07/19/2014'),
            '_19_07__09_08' => DateTime('08/09/2014'),
            '_09_08__23_08' => DateTime('08/23/2014'),
            '_23_08__06_09' => DateTime('09/06/2014'),
            '_06_09__20_09' => DateTime('09/20/2014'),
            '_20_09__04_10' => DateTime('10/04/2014'),
            '_04_10__18_10' => DateTime('10/18/2014'),
            '_18_10__01_11' => DateTime('11/01/2014'),
        );

        $keys = array_keys($dates);
        $from = DateTime('03/29/2014');

        foreach($keys as $key)
        {
            $to = $dates[$key];
            $p = $csv[$key] / 7;
            $this->boatService->addPrice($boat, $from, $to, $p, false, false);
            $from = clone $to;
        }

        // remaining price
        $to = DateTime('12/31/2014');
        $p = $csv['_01_11__'] / 7;
        $this->boatService->addPrice($boat, $from, $to, $p, false, false);
    }

    private function setBoatAssets($boat, $csv) {

        $assets = $this->amenities;
        $assetIds = array_keys($assets);
        foreach($assetIds as $id) {
            if($csv[$id] == "1") {
                $this->boatService->addAmenities($boat, $assets[$id], false);
            }
        }

        $assets = $this->equipment;
        $assetIds = array_keys($assets);
        foreach($assetIds as $id) {
            if($csv[$id] == "1") {
                $this->boatService->addEquipment($boat, $assets[$id], false);
            }
        }
    }

    private function setBoatImages($boat, $csv) {

        $base = dirname(__FILE__).'/Data//';
        $images = scandir($base.$csv->id);

        foreach($images as $image) {
            if(is_file($base.$image)) {
                $this->addImage($boat, $base.$image);
            }
        }
    }

    private function loadBoat($csv)
    {
        $boat = new Boat();

        $addresses = $this->getAddresses();
        $address = $addresses[$csv->base];
        $boat->setModel($csv->type);
        $boat->setName($csv->name);
        $boat->setYear($csv->year);
        $boat->setCabins($csv->cabins);
        $boat->setBerths($csv->berths);
        $boat->setNrGuests($csv->crew);
        $boat->setToilets($csv->toilets);
        $boat->setLength($csv->length);
        // $boat_csv->beam
        // $boat_csv->draught
        // $boat_csv->weight
        $boat->setWaterCapacity($csv->water_tank);
        $boat->setFuelCapacity($csv->fuel_tank);
        // $boat_csv->motor
        // $boat_csv->engine
        $boat->setDefaultPrice($csv->__29_3);
        $boat->setHasDefaultPrice(true);
        $boat->setActive(true);

        $boat = $this->boatService->createBoat(
            $boat,
            $address,
            $this->boatTypeRepo->findOneByName('Sailboat'),
            $this->charter);

        // Set the boat assets
        $this->setBoatAssets($boat, $csv);

        // Set the boat price
        $this->setBoatPrices($boat, $csv);
    }
    
    private function loadBoats()
    {    
        $boats_src= file_get_contents(dirname(__FILE__).'/Data/ayc_fleet.json');
        $boats_csv = json_decode($boats_src);
        
        foreach ($boats_csv as $boat_csv)
        {
            $this->loadBoat($boat_csv);
        }
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $this->container    = $this->getContainer();
        $this->em           = $this->container->get('doctrine.orm.entity_manager');
        $this->boatService  = $this->container->get('boat_service');
        $this->charterService  = $this->container->get('zizoo_charter_charter_service');
        $this->boatTypeRepo = $this->em->getRepository('ZizooBoatBundle:BoatType');
        $this->countryRepo = $this->em->getRepository('ZizooAddressBundle:Country');

        $this->amenities = $this->initAssets('ZizooBoatBundle:Amenities');
        $this->equipment = $this->initAssets('ZizooBoatBundle:Equipment');
        $this->extras=  $this->initAssets('ZizooBoatBundle:Extra');
        
        $this->loadCharter();
        $this->loadBoats();
        $this->em->flush();
    }
}
?>
