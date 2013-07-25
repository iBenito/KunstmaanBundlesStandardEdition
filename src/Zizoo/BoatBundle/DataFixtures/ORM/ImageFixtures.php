<?php

namespace Zizoo\BoatBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\SharedFixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Zizoo\BoatBundle\Entity\BoatImage;
use Zizoo\BoatBundle\Entity\Boat;

class ImageFixtures implements OrderedFixtureInterface, SharedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    private $imgPath;
    
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
        $this->imgPath = $this->container->getParameter('kernel.root_dir').'/../web/images/boat';
    }
    
    private function rrmdir($dir) { 
        foreach(glob($dir . '/*') as $file) { 
          if(is_dir($file)) $this->rrmdir($file); else unlink($file); 
        } 
        rmdir($dir); 
    }
    
    private function copyImage($boat, $image){
        $boatImgPath = $this->imgPath.'/'.$boat->getId().'/originals';
        
        if (!file_exists($boatImgPath)){
            mkdir($boatImgPath, 0775, true);
        } else if (!is_dir($boatImgPath)){
            unlink($boatImgPath);
            mkdir($boatImgPath, 0775, true);
        }
        
        $boatImgPath .= '/'.$image->getPath();
        
        if (file_exists($boatImgPath)){
            unlink($boatImgPath);
        } 
        
        $from = dirname(__FILE__).'/../Images/'.$image->getPath();
        echo "copy ". $from . " to " . $boatImgPath . "\n";
        copy($from, $boatImgPath);
    }
    
    public function load(ObjectManager $manager)
    {
        /*
        return;
        if (file_exists($this->imgPath)){
            if (is_dir($this->imgPath)){
                $this->rrmdir($this->imgPath);
            } else {
                unlink($this->imgPath);
            }
        }

        mkdir($this->imgPath, 0775, true);
        
        $boat = $this->getReference('boat-1');
        $image = new BoatImage();
        $image->setBoat($manager->merge($boat));
        $image->setPath('1.jpg');
        $image->setMimeType('image/jpeg');

        $manager->persist($image);
        //$this->copyImage($boat, '1', $image);
        
        $image = new BoatImage();
        $image->setBoat($manager->merge($boat));
        $image->setPath('2.jpg');
        $image->setMimeType('image/jpeg');

        $manager->persist($image);
        //$this->copyImage($boat, '1', $image);
        
        $boat = $this->getReference('boat-2');
        $image = new BoatImage();
        $image->setBoat($manager->merge($boat));
        $image->setPath('3.jpg');
        $image->setMimeType('image/jpeg');

        $manager->persist($image);
        //$this->copyImage($boat, '2', $image);
        */

        $boat = $this->getReference('boat-5');
        for($i=1; $i<4; $i++) {
            $image = new BoatImage();
            $image->setBoat($manager->merge($boat));
            $image->setMimeType('image/jpeg');
            $image->setPath("elan_$i.jpg");
            $manager->persist($image);
            $this->copyImage($boat, $image);
        }

        $boat = $this->getReference('boat-6');
        for($i=1; $i<4; $i++) {
            $image = new BoatImage();
            $image->setBoat($manager->merge($boat));
            $image->setPath("first31_1.jpg");
            $image->setMimeType('image/jpeg');
            $manager->persist($image);
            $this->copyImage($boat, $image);
        }

        $boat = $this->getReference('boat-7');
        for($i=1; $i<4; $i++) {
            $image = new BoatImage();
            $image->setBoat($manager->merge($boat));
            $image->setPath("bavaria$i.jpg");
            $image->setMimeType('image/jpeg');
            $manager->persist($image);
            $this->copyImage($boat, $image);
        }

        $boat = $this->getReference('boat-8');
        for($i=1; $i<4; $i++) {
            $image = new BoatImage();
            $image->setBoat($manager->merge($boat));
            $image->setPath("first21_$i.jpg");
            $image->setMimeType('image/jpeg');
            $manager->persist($image);
            $this->copyImage($boat, $image);
        }

        $boat = $this->getReference('boat-9');
        for($i=1; $i<4; $i++) {
            $image = new BoatImage();
            $image->setBoat($manager->merge($boat));
            $image->setPath("oceanis$i.jpg");
            $image->setMimeType('image/jpeg');
            $manager->persist($image);
            $this->copyImage($boat, $image);
        }

        $boat = $this->getReference('boat-10');
        for($i=1; $i<4; $i++) {
            $image = new BoatImage();
            $image->setBoat($manager->merge($boat));
            $image->setPath("odyssey$i.jpg");
            $image->setMimeType('image/jpeg');
            $manager->persist($image);
            $this->copyImage($boat, $image);
        }
        
        $manager->flush();

    }

    public function getOrder()
    {
        return 5;
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