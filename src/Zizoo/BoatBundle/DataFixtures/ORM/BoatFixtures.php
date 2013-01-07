<?php

namespace Zizoo\BoatBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\AddressBundle\Entity\BoatAddress;
use Zizoo\AddressBundle\Entity\Country;
use Zizoo\BookingBundle\Entity\Reservation;

class BoatFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $boat1 = new Boat();
        $boat1->setName('Sandali');
        $boat1->setTitle('The Ocean Explorer');
        $boat1->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.');
        $boat1->setBrand('Seasy');
        $boat1->setModel('911');
        $boat1->setLength('5');
        $boat1->setCabins('6');
        $boat1->setNrGuests('12');
        $boat1->setStatus('0');
        $boat1->setCreated(new \DateTime());
        $boat1->setUpdated($boat1->getCreated());
        
        $boat1Address = new BoatAddress();
        $boat1Address->setStreet('Krk Marina');
        $boat1Address->setPremise('48');
        $boat1Address->setLocality('Krk');
        $boat1Address->setPostcode('54321');
        $boat1Address->setCountry($manager->merge($this->getReference('countryHR')));
        $boat1Address->fetchGeo();
        $boat1Address->setBoat($boat1);        
        
        $manager->persist($boat1Address);
        $manager->persist($boat1);
        
        $boat2 = new Boat();
        $boat2->setName('Infinity');
        $boat2->setTitle('Forever Wherever');
        $boat2->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.');
        $boat2->setBrand('Floats');
        $boat2->setModel('919');
        $boat2->setLength('12');
        $boat2->setCabins('5');
        $boat2->setNrGuests('11');
        $boat2->setStatus('1');
        $boat2->setCreated(new \DateTime());
        $boat2->setUpdated($boat2->getCreated());
        
        $boat2Address = new BoatAddress();
        $boat2Address->setStreet('Alicante Marina');
        $boat2Address->setPremise('84');
        $boat2Address->setLocality('Alicante');
        $boat2Address->setPostcode('12345');
        $boat2Address->setProvince('Some spanish province');
        $boat2Address->setCountry($manager->merge($this->getReference('countryES')));
        $boat2Address->fetchGeo();
        $boat2Address->setBoat($boat2);
        
        $boat2Address2 = new BoatAddress();
        $boat2Address2->setStreet('Hafenstr.');
        $boat2Address2->setPremise('84');
        $boat2Address2->setLocality('Hamburg');
        $boat2Address2->setPostcode('69429');
        $boat2Address2->setProvince('North');
        $boat2Address2->setCountry($manager->merge($this->getReference('countryDE')));
        $boat2Address2->fetchGeo();
        $boat2Address2->setBoat($boat2);
        
        $boat2Address3 = new BoatAddress();
        $boat2Address3->setStreet('Uferstr.');
        $boat2Address3->setPremise('77');
        $boat2Address3->setLocality('Kiel');
        $boat2Address3->setPostcode('12234');
        $boat2Address3->setProvince('Kiel');
        $boat2Address3->setSubLocality('Kiel');
        $boat2Address3->setCountry($manager->merge($this->getReference('countryDE')));
        $boat2Address3->fetchGeo();
        $boat2Address3->setBoat($boat2);

        $manager->persist($boat2Address);
        $manager->persist($boat2Address2);
        $manager->persist($boat2Address3);
        $manager->persist($boat2);

        
        $boat3 = new Boat();
        $boat3->setName('Serenity');
        $boat3->setTitle('Firefly');
        $boat3->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.');
        $boat3->setBrand('Seasy');
        $boat3->setModel('911');
        $boat3->setLength('5');
        $boat3->setCabins('6');
        $boat3->setNrGuests('20');
        $boat3->setStatus('0');
        $boat3->setCreated(new \DateTime());
        $boat3->setUpdated($boat3->getCreated());
        
        $boat3Address = new BoatAddress();
        $boat3Address->setStreet('Brighton Marina');
        $boat3Address->setPremise('48');
        $boat3Address->setLocality('Brighton');
        $boat3Address->setPostcode('BN1 1NB');
        $boat3Address->setCountry($manager->merge($this->getReference('countryGB')));
        $boat3Address->fetchGeo();
        $boat3Address->setBoat($boat3);      
        
        $manager->persist($boat3Address);
        $manager->persist($boat3);
        
        
        $boat4 = new Boat();
        $boat4->setName('Enterprise');
        $boat4->setTitle('TNG');
        $boat4->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.');
        $boat4->setBrand('Seasy');
        $boat4->setModel('911');
        $boat4->setLength('5');
        $boat4->setCabins('6');
        $boat4->setNrGuests('20');
        $boat4->setStatus('0');
        $boat4->setCreated(new \DateTime());
        $boat4->setUpdated($boat4->getCreated());
        
        $boat4Address = new BoatAddress();
        $boat4Address->setStreet('Bristol Harbour');
        $boat4Address->setPremise('48');
        $boat4Address->setLocality('Bristol');
        $boat4Address->setPostcode('BS1 5QA');
        $boat4Address->setCountry($manager->merge($this->getReference('countryGB')));
        $boat4Address->fetchGeo();
        $boat4Address->setBoat($boat4);  
        
        
        $manager->persist($boat4Address);
        $manager->persist($boat4);
        
        $manager->flush();

        $this->addReference('boat-1', $boat1);
        $this->addReference('boat-2', $boat2);
        $this->addReference('boat-3', $boat3);
    }

    public function getOrder()
    {
        return 2;
    }

}