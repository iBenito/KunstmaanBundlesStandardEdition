<?php

namespace Zizoo\ProfileBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\UserBundle\Entity\User;

class ProfileFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $profile = new Profile();
        $profile->setFirstName('Alex');
        $profile->setLastName('Bomba');
        $profile->setAbout('This and that');
        $profile->setCountry('Germany');
        $profile->setCity('Waldbrunn');
        $profile->setAddress('Waldbrunn Fufzig');
        $profile->setPhone('004444444444');
        $profile->setPicture('alex_1.png');
        $profile->setCreated(new \DateTime());
        $profile->setUpdated($profile->getCreated());
        $profile->setUser($manager->merge($this->getReference('user-1')));
        $manager->persist($profile);
        
        $profile = new Profile();
        $profile->setFirstName('Benny');
        $profile->setLastName('Gonzo');
        $profile->setAbout('If I am not me, then who de hell am I');
        $profile->setCountry('Austria');
        $profile->setCity('Wien');
        $profile->setAddress('Wien achzig');
        $profile->setPhone('006666666');
        $profile->setPicture('benny_1.png');
        $profile->setCreated(new \DateTime());
        $profile->setUpdated($profile->getCreated());
        $profile->setUser($manager->merge($this->getReference('user-2')));
        $manager->persist($profile);
        
        $manager->flush();

    }

    public function getOrder()
    {
        return 3;
    }

}