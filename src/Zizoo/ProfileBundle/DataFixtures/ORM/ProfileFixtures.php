<?php

namespace Zizoo\ProfileBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\UserBundle\Entity\User;
use Zizoo\AddressBundle\Entity\ProfileAddress;
use Zizoo\AddressBundle\Entity\Country;

class ProfileFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        
        $profile = new Profile();
        $profile->setFirstName('Zizoo');
        $profile->setLastName('Register');
        $profile->setCreated(new \DateTime());
        $profile->setUpdated($profile->getCreated());
        $profile->setUser($manager->merge($this->getReference('user-register')));
        
        $profileAddress = new ProfileAddress();
        $profileAddress->setCountry($manager->merge($this->getReference('countryAT')));
        $profileAddress->setProfile($profile);
        
        $manager->persist($profileAddress);
        $manager->persist($profile);
        
        $profile = new Profile();
        $profile->setFirstName('Zizoo');
        $profile->setLastName('Info');
        $profile->setCreated(new \DateTime());
        $profile->setUpdated($profile->getCreated());
        $profile->setUser($manager->merge($this->getReference('user-info')));
        
        $profileAddress = new ProfileAddress();
        $profileAddress->setCountry($manager->merge($this->getReference('countryAT')));
        $profileAddress->setProfile($profile);
        
        $manager->persist($profileAddress);
        $manager->persist($profile);
        
        $profile = new Profile();
        $profile->setFirstName('Alex');
        $profile->setLastName('Bomba');
        $profile->setAbout('This and that');
        $profile->setPhone('004444444444');
        $profile->setPicture('alex_1.png');
        $profile->setCreated(new \DateTime());
        $profile->setUpdated($profile->getCreated());
        $profile->setUser($manager->merge($this->getReference('user-1')));
        
        $profileAddress = new ProfileAddress();
        $profileAddress->setStreet('Im Bräunlesrot');
        $profileAddress->setPremise('20');
        $profileAddress->setLocality('Waldbrunn');
        $profileAddress->setPostcode('69429');
        $profileAddress->setCountry($manager->merge($this->getReference('countryDE')));
        $profileAddress->setProfile($profile);
        
        $profileAddress2 = new ProfileAddress();
        $profileAddress2->setStreet('Brusselsstreet');
        $profileAddress2->setPremise('20');
        $profileAddress2->setLocality('Bruxelles');
        $profileAddress2->setPostcode('1050');
        $profileAddress2->setCountry($manager->merge($this->getReference('countryBE')));
        $profileAddress2->setProfile($profile);
        
        $manager->persist($profileAddress);
        $manager->persist($profileAddress2);
        $manager->persist($profile);
        
        $this->addReference('profile-1', $profile);
        
        $profile = new Profile();
        $profile->setFirstName('Benito');
        $profile->setLastName('Gonzo');
        $profile->setAbout('If I am not me, then who de hell am I');
        $profile->setPhone('006666666');
        $profile->setPicture('benny_1.png');
        $profile->setCreated(new \DateTime());
        $profile->setUpdated($profile->getCreated());
        $profile->setUser($manager->merge($this->getReference('user-2')));
        
        $profileAddress = new ProfileAddress();
        $profileAddress->setStreet('Wienerstr.');
        $profileAddress->setPremise('80');
        $profileAddress->setLocality('Wien');
        $profileAddress->setPostcode('77777');
        $profileAddress->setCountry($manager->merge($this->getReference('countryAT')));
        $profileAddress->setProfile($profile);
        
        $manager->persist($profileAddress);
        $manager->persist($profile);
        
        $this->addReference('profile-2', $profile);
        
        $profile = new Profile();
        $profile->setFirstName('Sinan');
        $profile->setLastName('Masovic');
        $profile->setCreated(new \DateTime());
        $profile->setUpdated($profile->getCreated());
        $profile->setUser($manager->merge($this->getReference('user-3')));
        
        $profileAddress = new ProfileAddress();
        
        $profileAddress->setLocality('Wien');
        $profileAddress->setCountry($manager->merge($this->getReference('countryAT')));
        $profileAddress->setProfile($profile);
        
        $manager->persist($profileAddress);
        $manager->persist($profile);
        
        $this->addReference('profile-3', $profile);
        
        
        $profile = new Profile();
        $profile->setFirstName('Tilen');
        $profile->setLastName('Travnik');
        $profile->setCreated(new \DateTime());
        $profile->setUpdated($profile->getCreated());
        $profile->setUser($manager->merge($this->getReference('user-4')));
        
        $profileAddress = new ProfileAddress();
        
        $profileAddress->setCountry($manager->merge($this->getReference('countrySI')));
        $profileAddress->setProfile($profile);
        
        $manager->persist($profileAddress);
        $manager->persist($profile);
        
        $this->addReference('profile-4', $profile);
        
        $manager->flush();

    }

    public function getOrder()
    {
        return 3;
    }

}