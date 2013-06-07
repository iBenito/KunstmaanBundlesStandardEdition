<?php
// src/Zizoo/UserBundle/DataFixtures/ORM/GroupFixtures.php

namespace ZizoozUserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zizoo\UserBundle\Entity\Group;

class GroupFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        
//        $group_zizoo = new Group();
//        $group_zizoo->setId('ROLE_ZIZOO_USER');
//        $group_zizoo->setRole('ROLE_ZIZOO_USER');
//        
//        $group_zizoo_charter = new Group();
//        $group_zizoo_charter->setId('ROLE_ZIZOO_CHARTER');
//        $group_zizoo_charter->setRole('ROLE_ZIZOO_CHARTER');
//        
//        $group_zizoo_charter_admin = new Group();
//        $group_zizoo_charter_admin->setId('ROLE_ZIZOO_CHARTER_ADMIN');
//        $group_zizoo_charter_admin->setRole('ROLE_ZIZOO_CHARTER_ADMIN');
//        
//        $group_zizoo_admin = new Group();
//        $group_zizoo_admin->setId('ROLE_ZIZOO_ADMIN');
//        $group_zizoo_admin->setRole('ROLE_ZIZOO_ADMIN');
//        
//        $group_zizoo_super_admin = new Group();
//        $group_zizoo_super_admin->setId('ROLE_ZIZOO_SUPER_ADMIN');
//        $group_zizoo_super_admin->setRole('ROLE_ZIZOO_SUPER_ADMIN');
//        
//        $manager->persist($group_zizoo);
//        $manager->persist($group_zizoo_charter);
//        $manager->persist($group_zizoo_charter_admin);
//        $manager->persist($group_zizoo_admin);
//        $manager->persist($group_zizoo_super_admin);
//        
//        $manager->flush();
//        
//        $this->addReference('group_user', $group_zizoo);
//        $this->addReference('group_charter', $group_zizoo_charter);
//        $this->addReference('group_charter_admin', $group_zizoo_charter_admin);
//        $this->addReference('group_admin', $group_zizoo_admin);
//        $this->addReference('group_super_admin', $group_zizoo_super_admin);

    }
    
    public function getOrder()
    {
        return 1;
    }

}
?>