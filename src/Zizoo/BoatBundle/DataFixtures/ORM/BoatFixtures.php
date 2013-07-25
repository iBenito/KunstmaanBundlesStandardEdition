<?php

namespace Zizoo\BoatBundle\DataFixtures\ORM;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Entity\Price;
use Zizoo\AddressBundle\Entity\BoatAddress;
use Zizoo\AddressBundle\Entity\Country;
use Zizoo\ReservationBundle\Entity\Reservation;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\SharedFixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Common\Collections\ArrayCollection;

class BoatFixtures implements OrderedFixtureInterface, SharedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * Fixture reference repository
     * 
     * @var ReferenceRepository
     */
    protected $referenceRepository;
    
    /**
     * {@inheritdoc}
     */
    public function setReferenceRepository(ReferenceRepository $referenceRepository)
    {
        $this->referenceRepository = $referenceRepository;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    public function load(ObjectManager $manager)
    {
        $boatService    = $this->container->get('boat_service');
        $em             = $this->container->get('doctrine.orm.entity_manager');
        $boatTypeRepo   = $em->getRepository('ZizooBoatBundle:BoatType');   
        $equipmentRepo  = $em->getRepository('ZizooBoatBundle:Equipment');   
        $countryRepo    = $em->getRepository('ZizooAddressBundle:Country');
        
        $equipmentMainsailFurling   = $equipmentRepo->findOneById('mainsail_furning');
        $equipmentBattenedMainsail  = $equipmentRepo->findOneById('battened_mainsail');
        $equipmentTeakDeck          = $equipmentRepo->findOneById('teak_deck');
        
        $charter1 = $manager->merge($this->getReference('charter-1'));
        $charter2 = $manager->merge($this->getReference('charter-2'));
        
        $boat1 = new Boat();
        $boat1->setName('Sandali');
        $boat1->setTitle('The Ocean Explorer');
        $boat1->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.');
        $boat1->setBrand('Seasy');
        $boat1->setModel('911');
        $boat1->setLength(5);
        $boat1->setCabins(6);
        $boat1->setBerths(6);
        $boat1->setBathrooms(6);
        $boat1->setToilets(6);
        $boat1->setNrGuests(12);
        $boat1->setDefaultPrice(9.99);        
        
        $boat1Address = new BoatAddress();
        $boat1Address->setAddressLine1('Krk Marina 48');
        $boat1Address->setLocality('Krk');
        $boat1Address->setPostcode('54321');
        $boat1Address->setCountry($countryRepo->findOneByIso('HR'));
        
        $boat1 = $boatService->createBoat($boat1, $boat1Address, $boatTypeRepo->findOneByName('Yacht'), $charter1, new ArrayCollection(array($equipmentBattenedMainsail, $equipmentMainsailFurling)));
//        
//        $from = new \DateTime();
//        $from->modify( 'first day of last month' );
//        $to = new \DateTime();
//        $to->modify( 'last day of next month' );
//        $boatService->addPrice($boat1, $from, $to, 9.99, false, true);
//        
//        $from = clone $to;
//        $from->modify( '+1 day' );
//        $to = clone $from;
//        $to->modify( '+1 month' );
//        //$boatService->addPrice($boat1, $from, $to, 299.99, false, true);
//        
        $manager->persist($boat1);
        $this->addReference('boat-1', $boat1);
        
        $boat2 = new Boat();
        $boat2->setName('Infinity');
        $boat2->setTitle('Forever Wherever');
        $boat2->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.');
        $boat2->setBrand('Floats');
        $boat2->setModel('912');
        $boat2->setLength(12);
        $boat2->setCabins(5);
        $boat2->setBerths(5);
        $boat2->setBathrooms(2);
        $boat2->setToilets(1);
        $boat2->setNrGuests(1);
        $boat2->setDefaultPrice(11.99);
        
        $boat2Address = new BoatAddress();
        $boat2Address->setAddressLine1('Alicante Marina 84');
        $boat2Address->setLocality('Alicante');
        $boat2Address->setPostcode('12345');
        $boat2Address->setProvince('Some spanish province');
        $boat2Address->setCountry($countryRepo->findOneByIso('ES'));
        
        $boat2 = $boatService->createBoat($boat2, $boat2Address, $boatTypeRepo->findOneByName('Yacht'), $charter1);
        $manager->persist($boat2);
        
        $this->addReference('boat-2', $boat2);
        
        
        $boat3 = new Boat();
        $boat3->setName('Serenity');
        $boat3->setTitle('Firefly');
        $boat3->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.');
        $boat3->setBrand('Floats');
        $boat3->setModel('911');
        $boat3->setLength(5);
        $boat3->setCabins(6);
        $boat3->setBerths(5);
        $boat3->setBathrooms(2);
        $boat3->setToilets(2);
        $boat3->setNrGuests(20);
        $boat3->setDefaultPrice(99.99);
        
        $boat3Address = new BoatAddress();
        $boat3Address->setAddressLine1('Brighton Marina 48');
        $boat3Address->setLocality('Brighton');
        $boat3Address->setPostcode('BN1 1NB');
        $boat3Address->setCountry($countryRepo->findOneByIso('GB'));   
        

        $boat3 = $boatService->createBoat($boat3, $boat3Address, $boatTypeRepo->findOneByName('Yacht'), $charter2);
        $manager->persist($boat3);
        
        
        $this->addReference('boat-3', $boat3);
        
        $boat4 = new Boat();
        $boat4->setName('Enterprise');
        $boat4->setTitle('TNG');
        $boat4->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.');
        $boat4->setBrand('Seasy');
        $boat4->setModel('911');
        $boat4->setLength(50);
        $boat4->setCabins(20);
        $boat4->setBerths(20);
        $boat4->setBathrooms(9);
        $boat4->setToilets(4);
        $boat4->setNrGuests(40);
        $boat4->setDefaultPrice(10000);
        
        $boat4Address = new BoatAddress();
        $boat4Address->setAddressLine1('Bristol Harbour 48');
        $boat4Address->setLocality('Bristol');
        $boat4Address->setPostcode('BS1 5QA');
        $boat4Address->setCountry($countryRepo->findOneByIso('GB'));
        
        $boat4 = $boatService->createBoat($boat4, $boat4Address, $boatTypeRepo->findOneByName('Yacht'), $charter2);
        $manager->persist($boat4);
        
        $this->addReference('boat-4', $boat4);



        $boat5 = new Boat();
        $boat5->setName('Elanusha');
        $boat5->setTitle('TNG');
        $boat5->setDescription('The Elan 31 from Elan Yachts is a great Sailing boat available for charter in Croatia. With 2 cabins and 6 berths, it has the ability to cater up to people overnight. This makes it a perfect choice for small groups wishing to charter the region. Book online for a great sailing holiday in Croatia.');
        $boat5->setBrand('Elan Yachts ');
        $boat5->setModel('Elan 31');
        $boat5->setLength(9.45);
        $boat5->setCabins(2);
        $boat5->setBerths(6);
        $boat5->setBathrooms(2);
        $boat5->setToilets(2);
        $boat5->setNrGuests(6);
        $boat5->setDefaultPrice(10000);
        
        $boat5Address = new BoatAddress();
        $boat5Address->setAddressLine1('Hvar');
        $boat5Address->setLocality('Hvar');
        $boat5Address->setPostcode('HR 21450');
        $boat5Address->setCountry($countryRepo->findOneByIso('HR'));
        
        $boat5 = $boatService->createBoat($boat5, $boat5Address, $boatTypeRepo->findOneByName('Sailboat'), $charter1);
        $manager->persist($boat5);
        
        $this->addReference('boat-5', $boat5);

        $boat6 = new Boat();
        $boat6->setName('Enterprise');
        $boat6->setTitle('TNG');
        $boat6->setDescription('The First 31.7 from Beneteau is a great Sailing boat available for charter in Italy. With 2 cabins and 4 berths, it has the ability to cater up to people overnight. This makes it a perfect choice for small groups wishing to charter the region. Book online for a great sailing holiday in Italy.');
        $boat6->setBrand('Beneteau');
        $boat6->setModel('First 31.7 ');
        $boat6->setLength(9.85);
        $boat6->setCabins(2);
        $boat6->setBerths(4);
        $boat6->setBathrooms(2);
        $boat6->setToilets(2);
        $boat6->setNrGuests(4);
        $boat6->setDefaultPrice(10000);
        
        $boat6Address = new BoatAddress();
        $boat6Address->setAddressLine1('Capri');
        $boat6Address->setLocality('Capri');
        $boat6Address->setPostcode('80073');
        $boat6Address->setCountry($countryRepo->findOneByIso('IT'));
        
        $boat6 = $boatService->createBoat($boat6, $boat6Address, $boatTypeRepo->findOneByName('Sailboat'), $charter2);
        $manager->persist($boat6);
        
        $this->addReference('boat-6', $boat6);

        $boat7 = new Boat();
        $boat7->setName('Enterprise');
        $boat7->setTitle('TNG');
        $boat7->setDescription('The Bavaria 38 from Bavaria Yachtbau is a great Sailing boat available for charter in Greece. With 3 cabins and 6 berths, it has the ability to cater up to 8 people overnight. This makes it a perfect choice for small groups wishing to charter the region. Book online for a great sailing holiday in Greece.');
        $boat7->setBrand('Bavaria Yachtbau');
        $boat7->setModel('Bavaria 38');
        $boat7->setLength(11.85);
        $boat7->setCabins(3);
        $boat7->setBerths(6);
        $boat7->setBathrooms(3);
        $boat7->setToilets(3);
        $boat7->setNrGuests(8);
        $boat7->setDefaultPrice(10000);
        
        $boat7Address = new BoatAddress();
        $boat7Address->setAddressLine1('Crete');
        $boat7Address->setLocality('Crete');
        $boat7Address->setPostcode('730 04');
        $boat7Address->setCountry($countryRepo->findOneByIso('GR'));
        
        $boat7 = $boatService->createBoat($boat7, $boat7Address, $boatTypeRepo->findOneByName('Sailboat'), $charter2);
        $manager->persist($boat7);
        
        $this->addReference('boat-7', $boat7);

        $boat8 = new Boat();
        $boat8->setName('Enterprise');
        $boat8->setTitle('TNG');
        $boat8->setDescription('The First 21.7 from Beneteau is a great Sailing boat available for charter in Italy. With 2 cabins and 4 berths, it has the ability to cater up to people overnight. This makes it a perfect choice for small groups wishing to charter the region. Book online for a great sailing holiday in Italy.');
        $boat8->setBrand('Beneteau');
        $boat8->setModel('First 21.7');
        $boat8->setLength(6.4);
        $boat8->setCabins(1);
        $boat8->setBerths(2);
        $boat8->setBathrooms(1);
        $boat8->setToilets(1);
        $boat8->setNrGuests(2);
        $boat8->setDefaultPrice(10000);
        
        $boat8Address = new BoatAddress();
        $boat8Address->setAddressLine1('Mallorca');
        $boat8Address->setLocality('Mallorca');
        $boat8Address->setPostcode('07410');
        $boat8Address->setCountry($countryRepo->findOneByIso('ES'));
        
        $boat8 = $boatService->createBoat($boat8, $boat8Address, $boatTypeRepo->findOneByName('Yacht'), $charter1);
        $manager->persist($boat8);
        
        $this->addReference('boat-8', $boat8);

        $boat9 = new Boat();
        $boat9->setName('Enterprise');
        $boat9->setTitle('TNG');
        $boat9->setDescription('The Sun Odyssey 29.2 from Jeanneau is a great Sailing boat available for charter in Greece. With 2 cabins and 4 berths, it has the ability to cater up to 4 people overnight. This makes it a perfect choice for small groups wishing to charter the region. Book online for a great sailing holiday in Greece.');
        $boat9->setBrand('Jeanneau');
        $boat9->setModel('Sun Odyssey 29.2');
        $boat9->setLength(8.8);
        $boat9->setCabins(4);
        $boat9->setBerths(4);
        $boat9->setBathrooms(4);
        $boat9->setToilets(4);
        $boat9->setNrGuests(4);
        $boat9->setDefaultPrice(10000);
        
        $boat9Address = new BoatAddress();
        $boat9Address->setAddressLine1('Crete');
        $boat9Address->setLocality('Crete');
        $boat9Address->setPostcode('730 04');
        $boat9Address->setCountry($countryRepo->findOneByIso('GR'));
        
        $boat9 = $boatService->createBoat($boat9, $boat9Address, $boatTypeRepo->findOneByName('Sailboat'), $charter2);
        $manager->persist($boat9);
        
        $this->addReference('boat-9', $boat9);

        $boat10 = new Boat();
        $boat10->setName('Enterprise');
        $boat10->setTitle('TNG');
        $boat10->setDescription('The Oceanis 31 from Beneteau is a great Sailing boat available for charter in Greece. With 2 cabins and 4 berths, it has the ability to cater up to 4 people overnight. This makes it a perfect choice for small groups wishing to charter the region. Book online for a great sailing holiday in Greece.');
        $boat10->setBrand('Beneteau');
        $boat10->setModel('Oceanis 31 ');
        $boat10->setLength(50);
        $boat10->setCabins(2);
        $boat10->setBerths(4);
        $boat10->setBathrooms(2);
        $boat10->setToilets(2);
        $boat10->setNrGuests(4);
        $boat10->setDefaultPrice(10000);
        
        $boat10Address = new BoatAddress();
        $boat10Address->setAddressLine1('Zakynthos');
        $boat10Address->setLocality('Zakynthos');
        $boat10Address->setPostcode('291 00');
        $boat10Address->setCountry($countryRepo->findOneByIso('GR'));
        
        $boat10 = $boatService->createBoat($boat10, $boat10Address, $boatTypeRepo->findOneByName('Sailboat'), $charter1);
        $manager->persist($boat10);
        
        $this->addReference('boat-10', $boat10);
        
        $manager->flush();
        
    }

    public function getOrder()
    {
        return 4;
    }
    
    /**
     * Set the reference entry identified by $name
     * and referenced to managed $object. If $name
     * already is set, it overrides it
     * 
     * @param string $name
     * @param object $object - managed object
     * @see Doctrine\Common\DataFixtures\ReferenceRepository::setReference
     * @return void
     */
    public function setReference($name, $object)
    {
        $this->referenceRepository->setReference($name, $object);
    }
    
    /**
     * Set the reference entry identified by $name
     * and referenced to managed $object. If $name
     * already is set, it overrides it
     * 
     * @param string $name
     * @param object $object - managed object
     * @see Doctrine\Common\DataFixtures\ReferenceRepository::addReference
     * @return void
     */
    public function addReference($name, $object)
    {
        $this->referenceRepository->addReference($name, $object);
    }
    
    /**
     * Loads an object using stored reference
     * named by $name
     * 
     * @param string $name
     * @see Doctrine\Common\DataFixtures\ReferenceRepository::getReference
     * @return object
     */
    public function getReference($name)
    {
        return $this->referenceRepository->getReference($name);
    }
    
    /**
     * Check if an object is stored using reference
     * named by $name
     * 
     * @param string $name
     * @see Doctrine\Common\DataFixtures\ReferenceRepository::hasReference
     * @return boolean
     */
    public function hasReference($name)
    {
        return $this->referenceRepository->hasReference($name);
    }

}