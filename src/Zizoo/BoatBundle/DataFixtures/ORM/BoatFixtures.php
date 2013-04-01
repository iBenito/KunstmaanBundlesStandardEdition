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
        $boatTypeRepo   = $this->container->get('doctrine.orm.entity_manager')->getRepository('ZizooBoatBundle:BoatType');   
        $equipmentRepo  = $this->container->get('doctrine.orm.entity_manager')->getRepository('ZizooBoatBundle:Equipment');   
        
        $equipmentMainsailFurling   = $equipmentRepo->findOneById('mainsail_furning');
        $equipmentBattenedMainsail  = $equipmentRepo->findOneById('battened_mainsail');
        $equipmentTeakDeck          = $equipmentRepo->findOneById('teak_deck');
        
        $boat1 = new Boat();
        $boat1->setName('Sandali');
        $boat1->setTitle('The Ocean Explorer');
        $boat1->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.');
        $boat1->setBrand('Seasy');
        $boat1->setModel('911');
        $boat1->setLength(5);
        $boat1->setCabins(6);
        $boat1->setNrGuests(12);
        $boat1->setDefaultPrice(9.99);        
        
        $boat1Address = new BoatAddress();
        $boat1Address->setStreet('Krk Marina');
        $boat1Address->setPremise('48');
        $boat1Address->setLocality('Krk');
        $boat1Address->setPostcode('54321');
        $boat1Address->setCountry($manager->merge($this->getReference('countryHR')));
   
        $boat1 = $boatService->createBoat($boat1, $boat1Address, $boatTypeRepo->findOneByName('Yacht'), new ArrayCollection(array($equipmentBattenedMainsail, $equipmentMainsailFurling)));
        
        $from = new \DateTime();
        $from->modify( 'first day of last month' );
        $to = new \DateTime();
        $to->modify( 'last day of next month' );
        $boatService->addPrice($boat1, $from, $to, 9.99, false, true);
        
        $from = clone $to;
        $from->modify( '+1 day' );
        $to = clone $from;
        $to->modify( '+1 month' );
        $boatService->addPrice($boat1, $from, $to, 299.99, false, true);
        
        $boat1->setUser($manager->merge($this->getReference('user-1')));
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
        $boat2->setNrGuests(1);
        $boat2->setDefaultPrice(11.99);
        
        $boat2Address = new BoatAddress();
        $boat2Address->setStreet('Alicante Marina');
        $boat2Address->setPremise('84');
        $boat2Address->setLocality('Alicante');
        $boat2Address->setPostcode('12345');
        $boat2Address->setProvince('Some spanish province');
        $boat2Address->setCountry($manager->merge($this->getReference('countryES')));
        
        $boat2 = $boatService->createBoat($boat2, $boat2Address, $boatTypeRepo->findOneByName('Yacht'));
        
        $boat2->setUser($manager->merge($this->getReference('user-2')));
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
        $boat3->setNrGuests(20);
        $boat3->setDefaultPrice(99.99);
        
        $boat3Address = new BoatAddress();
        $boat3Address->setStreet('Brighton Marina');
        $boat3Address->setPremise('48');
        $boat3Address->setLocality('Brighton');
        $boat3Address->setPostcode('BN1 1NB');
        $boat3Address->setCountry($manager->merge($this->getReference('countryGB')));   
        

        $boat3 = $boatService->createBoat($boat3, $boat3Address, $boatTypeRepo->findOneByName('Yacht'));
        
        $boat3->setUser($manager->merge($this->getReference('user-2')));
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
        $boat4->setNrGuests(40);
        $boat4->setDefaultPrice(10000);
        
        $boat4Address = new BoatAddress();
        $boat4Address->setStreet('Bristol Harbour');
        $boat4Address->setPremise('48');
        $boat4Address->setLocality('Bristol');
        $boat4Address->setPostcode('BS1 5QA');
        $boat4Address->setCountry($manager->merge($this->getReference('countryGB')));
        
        $boat4 = $boatService->createBoat($boat4, $boat4Address, $boatTypeRepo->findOneByName('Yacht'));
        
        $boat4->setUser($manager->merge($this->getReference('user-2')));
        $manager->persist($boat4);
        
        $this->addReference('boat-4', $boat4);
        
        $manager->flush();
        /**
        // Load test
        for ($i=0; $i<100; $i++){
            $boat1Address = new BoatAddress();
            $boat1Address->setStreet('Krk Marina');
            $boat1Address->setPremise('48');
            $boat1Address->setLocality('Krk');
            $boat1Address->setPostcode('54321');
            $boat1Address->setCountry($manager->merge($this->getReference('countryHR')));

            $boat1Availability = new Availability();
            $boat1AvailabilityAddress = $boat1Address->createAvailabilityAddress();
            $boat1AvailabilityAddress->setAvailability($boat1Availability);
            $boat1Availability->setAddress($boat1AvailabilityAddress);
            $from = new \DateTime();
            $from->modify( 'first day of last month' );
            $from->setTime(0,0,0);
            $boat1Availability->setAvailableFrom($from);
            $to = new \DateTime();
            $to->modify( 'last day of next month' );
            $to->setTime(23,59,59);
            $boat1Availability->setAvailableUntil($to);
            $boat1Availability->setPrice(9.99);

            $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.';
            $boat1 = $boatService->createBoat('Sandali', 'The Ocean Explorer', $description, 'Seasy', '911', 5, 6, 12, $boat1Address, $boatTypeRepo->findOneByName('Yacht'), new ArrayCollection(array($boat1Availability)));
        }
         
         */
                
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