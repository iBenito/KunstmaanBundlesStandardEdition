<?php

namespace Zizoo\ProfileBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\SharedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\ProfileBundle\Entity\Profile\NotificationSettings;
use Zizoo\UserBundle\Entity\User;
use Zizoo\AddressBundle\Entity\ProfileAddress;
use Zizoo\AddressBundle\Entity\Country;

class ProfileFixtures2 extends AbstractFixture implements OrderedFixtureInterface, SharedFixtureInterface
{
    
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
    
    private function setAllNotificationSettings(NotificationSettings $notificationSettings, $set){
        $notificationSettings->setBooked($set);
        $notificationSettings->setBooking($set);
        $notificationSettings->setEnquiry($set);
        $notificationSettings->setMessage($set);
        $notificationSettings->setReview($set);
        return $notificationSettings;
    }
    
    public function load(ObjectManager $manager)
    {
        
        $profile = $this->getReference('profile-1');
        $this->setAllNotificationSettings($profile->getNotificationSettings(), true);
        $manager->persist($profile);
        
        $profile = $this->getReference('profile-2');
        $this->setAllNotificationSettings($profile->getNotificationSettings(), true);
        $manager->persist($profile);
        
        $profile = $this->getReference('profile-3');
        $this->setAllNotificationSettings($profile->getNotificationSettings(), true);
        $manager->persist($profile);
        
        $profile = $this->getReference('profile-4');
        $this->setAllNotificationSettings($profile->getNotificationSettings(), true);
        $manager->persist($profile);
        
        $manager->flush();

    }

    public function getOrder()
    {
        return 7;
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