<?php

namespace Zizoo\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zizoo\UserBundle\Entity\Profile;
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
        $profile->setUserId($manager->merge($this->getReference('user-1')));
        $manager->persist($profile);
        
        $manager->flush();

    }

    public function getOrder()
    {
        return 3;
    }

}