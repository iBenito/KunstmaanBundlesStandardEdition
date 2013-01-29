<?php

namespace Zizoo\BoatBundle\DataFixtures\ORM;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Entity\Availability;
use Zizoo\AddressBundle\Entity\BoatAddress;
use Zizoo\AddressBundle\Entity\AvailabilityAddress;
use Zizoo\AddressBundle\Entity\Country;
use Zizoo\BookingBundle\Entity\Reservation;

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
        
        $boatService        = $this->container->get('boat_service');
        $boatTypeRepo       = $this->container->get('doctrine.orm.entity_manager')->getRepository('ZizooBoatBundle:BoatType');
        
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
        $boat1 = $boatService->createBoat('Sandali', 'The Ocean Explorer', $description, 'Seasy', '911', 5, 6, 12, 
                                            $boat1Address, $boatTypeRepo->findOneByName('Yacht'), new ArrayCollection(array($boat1Availability)));
        
        $this->addReference('boat-1', $boat1);
        

        $boat2Address = new BoatAddress();
        $boat2Address->setStreet('Alicante Marina');
        $boat2Address->setPremise('84');
        $boat2Address->setLocality('Alicante');
        $boat2Address->setPostcode('12345');
        $boat2Address->setProvince('Some spanish province');
        $boat2Address->setCountry($manager->merge($this->getReference('countryES')));
        
        $boat2Availability = new Availability();
        $boat2AvailabilityAddress = $boat2Address->createAvailabilityAddress();
        $boat2AvailabilityAddress->setAvailability($boat2Availability);
        $boat2Availability->setAddress($boat2AvailabilityAddress);
        
        $from = new \DateTime();
        $from->modify( 'first day of january' );
        $from->setTime(0,0,0);
        $boat2Availability->setAvailableFrom($from);
        $to = new \DateTime();
        $to->modify( 'last day of march' );
        $to->setTime(23,59,59);
        $boat2Availability->setAvailableUntil($to);
        $boat2Availability->setPrice(6.66);
        
        $boat2Availability2 = new Availability();
        $boat2Availability2Address2 = $boat2Address->createAvailabilityAddress();
        $boat2Availability2Address2->setAvailability($boat2Availability2);
        $boat2Availability2->setAddress($boat2Availability2Address2);
        
        $from = new \DateTime();
        $from->modify( 'first day of april' );
        $from->setTime(0,0,0);
        $boat2Availability2->setAvailableFrom($from);
        $to = new \DateTime();
        $to->modify( 'last day of april' );
        $to->setTime(23,59,59);
        $boat2Availability2->setAvailableUntil($to);
        $boat2Availability2->setPrice(6.66);
        
        $boat2Availability3 = new Availability();
        $boat2Availability3Address = new AvailabilityAddress();
        $boat2Availability3Address->setStreet('Clarendon Road');
        $boat2Availability3Address->setPremise('64');
        $boat2Availability3Address->setLocality('Bristol');
        $boat2Availability3Address->setPostcode('BS6 7EU');
        $boat2Availability3Address->setProvince('Bristol');
        $boat2Availability3Address->setCountry($manager->merge($this->getReference('countryGB')));
        $boat2Availability3Address->setAvailability($boat2Availability3);
        $boat2Availability3->setAddress($boat2Availability3Address);
        
        $from = new \DateTime();
        $from->modify( 'first day of april' );
        $from->setTime(0,0,0);
        $boat2Availability3->setAvailableFrom($from);
        $to = new \DateTime();
        $to->modify( 'last day of april' );
        $to->setTime(23,59,59);
        $boat2Availability3->setAvailableUntil($to);
        $boat2Availability3->setPrice(6.66);
        
        $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.';
        $boat2 = $boatService->createBoat('Infinity', 'Forever Wherever', $description, 'Floats', '912', 12, 5, 11, 
                                            $boat2Address, $boatTypeRepo->findOneByName('Catamaran'), new ArrayCollection(array($boat2Availability, $boat2Availability2, $boat2Availability3)));
        
        $this->addReference('boat-2', $boat2);
        
        

        $boat3Address = new BoatAddress();
        $boat3Address->setStreet('Brighton Marina');
        $boat3Address->setPremise('48');
        $boat3Address->setLocality('Brighton');
        $boat3Address->setPostcode('BN1 1NB');
        $boat3Address->setCountry($manager->merge($this->getReference('countryGB')));   
        
        $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.';
        $boat3 = $boatService->createBoat('Serenity', 'Firefly', $description, 'Floats', '911', 5, 6, 20, 
                                            $boat3Address, $boatTypeRepo->findOneByName('Sailboat'));
        
        $this->addReference('boat-3', $boat3);
        

        $boat4Address = new BoatAddress();
        $boat4Address->setStreet('Bristol Harbour');
        $boat4Address->setPremise('48');
        $boat4Address->setLocality('Bristol');
        $boat4Address->setPostcode('BS1 5QA');
        $boat4Address->setCountry($manager->merge($this->getReference('countryGB')));
        
        $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.';
        $boat4 = $boatService->createBoat('Enterprise', 'TNG', $description, 'Seasy', '911', 50, 20, 40, 
                                            $boat4Address, $boatTypeRepo->findOneByName('Catamaran'));
        
        $this->addReference('boat-4', $boat4);
        
        
        
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
            $boat1 = $boatService->createBoat('Sandali', 'The Ocean Explorer', $description, 'Seasy', '911', 5, 6, 12, 
                                                $boat1Address, $boatTypeRepo->findOneByName('Yacht'), new ArrayCollection(array($boat1Availability)));
        }
                
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