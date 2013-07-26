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
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
    
    private function rrmdir($dir) 
    { 
        foreach(glob($dir . '/*') as $file) { 
          if(is_dir($file)) $this->rrmdir($file); else unlink($file); 
        } 
        rmdir($dir); 
    }
    
    private function copyImage($filename, $tempDir)
                {
        $from = dirname(__FILE__).'/../Images/'.$filename;
        $toFile = tempnam($tempDir, $filename); 
        echo "copy ". $from . " to " . $toFile . "\n";
        copy($from, $toFile);
        return $toFile;
    }
    
    private function addImage(Boat $boat, $filename, $j)
    {
        $tempDir = ini_get('upload_tmp_dir');
        $tmpFilename = $this->copyImage($j.'/'.$filename, $tempDir);
        $uploadedFile = new UploadedFile($tmpFilename, $filename, null, null, null, true);
        $boatService = $this->container->get('boat_service');
        $boatService->addImage($boat, $uploadedFile, false);
        $boat->setActive(true);
        $now = new \DateTime();
        $boat->setUpdated($now);
    }
    
    public function load(ObjectManager $manager)
    {
        for ($j=5; $j<=10; $j++){
            $boat = $this->getReference("boat-$j");
            for($i=1; $i<=3; $i++) {
                $this->addImage($boat, "$i.jpg", $j);
                $manager->persist($boat);
            }
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