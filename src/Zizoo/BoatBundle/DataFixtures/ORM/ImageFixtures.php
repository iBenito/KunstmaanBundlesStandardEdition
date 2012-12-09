<?php

namespace Zizoo\BoatBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zizoo\BoatBundle\Entity\Image;
use Zizoo\BoatBundle\Entity\Boat;

class ImageFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $image = new Image();
        $image->setBoat($manager->merge($this->getReference('boat-1')));
        $image->setPath('boat_1.png');
        $manager->persist($image);
        
        $image = new Image();
        $image->setBoat($manager->merge($this->getReference('boat-1')));
        $image->setPath('boat_2.png');
        $manager->persist($image);

        $image = new Image();
        $image->setBoat($manager->merge($this->getReference('boat-2')));
        $image->setPath('boat_1.png');
        $manager->persist($image);
        
        $manager->flush();

    }

    public function getOrder()
    {
        return 2;
    }

}