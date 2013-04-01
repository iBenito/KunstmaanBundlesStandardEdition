<?php
// src/Zizoo/UserBundle/DataFixtures/ORM/GroupFixtures.php

namespace ZizoozUserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zizoo\UserBundle\Entity\Group;
use Zizoo\UserBundle\Entity\User;

class GroupFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        
        $group_zizoo = new Group();
        $group_zizoo->setId('ROLE_ZIZOO_USER');
        $group_zizoo->setRole('ROLE_ZIZOO_USER');
        
        $group_admin = new Group();
        $group_admin->setId('ROLE_ZIZOO_ADMIN');
        $group_admin->setRole('ROLE_ZIZOO_ADMIN');
        
        //$group_zizoo->addUser($manager->merge($this->getReference('user1')));
        //$group_zizoo->addUser($manager->merge($this->getReference('user2')));
        
        $manager->persist($group_zizoo);
        $manager->persist($group_admin);
        
        $manager->flush();
        
        $this->addReference('group_user', $group_zizoo);
        $this->addReference('group_admin', $group_admin);

    }
    
    public function getOrder()
    {
        return 1;
    }

}
?>