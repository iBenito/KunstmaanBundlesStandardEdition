<?php

namespace Zizoo\AddressBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zizoo\AddressBundle\Entity\Marina;

class MarinaFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $getMarinas = file_get_contents(dirname(__FILE__).'/marinas.json');
       
        $marinas = json_decode($getMarinas);
        
        foreach ($marinas as $marina)
        {
            $marinaEntity = new Marina();
            $marinaEntity->setName($marina->name);
            $marinaEntity->setLat($marina->latitude);
            $marinaEntity->setLng($marina->longitude);
            
            $manager->persist($marinaEntity);
            
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 0;
    }

}