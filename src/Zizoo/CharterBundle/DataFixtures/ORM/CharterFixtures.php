<?php
// src/Zizoo/CharterBundle/DataFixtures/ORM/CharterFixtures.php

namespace ZizoozUserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zizoo\CharterBundle\Entity\Charter;

class CharterFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user1 = $manager->merge($this->getReference('user-1'));
        $user2 = $manager->merge($this->getReference('user-2'));
        
        $charter_1 = new Charter();
        $charter_1->setCharterName('Mega Charter');
        $charter_1->setCharterNumber('123');
        $charter_1->setAdminUser($user1);
        $charter_1->setBillingUser($user1);
        $charter_1->addUser($user1);
        $charter_1->addUser($user2);
        $user1->setCharter($charter_1);
        $user2->setCharter($charter_1);
        
        $this->addReference('charter-1', $charter_1);
        
        $charter_2 = new Charter();
        $charter_2->setCharterName('Boats Unlimited');
        $charter_2->setCharterNumber('456');
        $charter_2->setAdminUser($user2);
        $charter_2->setBillingUser($user2);
        $charter_2->addUser($user2);
        $user2->setCharter($charter_2);
        
        $this->addReference('charter-2', $charter_2);
        
        $manager->persist($charter_1);
        $manager->persist($charter_2);
        
        $manager->flush();

    }
    
    public function getOrder()
    {
        return 3;
    }

}
?>