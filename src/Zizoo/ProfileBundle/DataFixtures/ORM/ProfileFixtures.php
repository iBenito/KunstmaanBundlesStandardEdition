<?php

namespace Zizoo\ProfileBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\SharedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\ProfileBundle\Entity\Profile\NotificationSettings;
use Zizoo\UserBundle\Entity\User;
use Zizoo\AddressBundle\Entity\ProfileAddress;
use Zizoo\AddressBundle\Entity\Country;

class ProfileFixtures extends AbstractFixture implements OrderedFixtureInterface, SharedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * Fixture reference repository
     * 
     * @var ReferenceRepository
     */
    protected $referenceRepository;
    
    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    
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
    
    private function isNotificationUser($notificationUsers, $user){
        foreach ($notificationUsers as $notificationUser){
            if ($notificationUser == $user) {
                return true;
            }
        }
        
        return false;
    }
    
    private function initialisePaymentCustomers(){        
        $userService = $this->container->get('zizoo_user_user_service');
        
        $customers = \Braintree_Customer::all();
        foreach ($customers as $customer){
            \Braintree_Customer::delete($customer->id);
        }
        
        $userService->getPaymentUser($this->getReference('user-1'));
        $userService->getPaymentUser($this->getReference('user-2'));
        $userService->getPaymentUser($this->getReference('user-3'));
    }
    
    public function load(ObjectManager $manager)
    {        
        $notificationUsers = array();
        if (array_key_exists('notification_users', $this->container->parameters)){
            $notificationUsers = $this->container->parameters['notification_users'];
        }
        
//        $ref = 'user-register';
//        $profile = new Profile();
//        $profile->setFirstName('Zizoo');
//        $profile->setLastName('Register');
//        $profile->setCreated(new \DateTime());
//        $profile->setUpdated($profile->getCreated());
//        $profile->setUser($manager->merge($this->getReference($ref)));
//        if ($this->isNotificationUser($notificationUsers, $ref)){
//            $this->setAllNotificationSettings($profile->getNotificationSettings(), true);
//        }
//        
//        $profileAddress = new ProfileAddress();
//        $profileAddress->setCountry($manager->merge($this->getReference('countryAT')));
//        $profileAddress->setProfile($profile);
//                
//        $manager->persist($profileAddress);
//        $manager->persist($profile);
//        
//        $ref = 'user-info';
//        $profile = new Profile();
//        $profile->setFirstName('Zizoo');
//        $profile->setLastName('Info');
//        $profile->setCreated(new \DateTime());
//        $profile->setUpdated($profile->getCreated());
//        $profile->setUser($manager->merge($this->getReference($ref)));
//        if ($this->isNotificationUser($notificationUsers, $ref)){
//            $this->setAllNotificationSettings($profile->getNotificationSettings(), true);
//        }
//        
//        $profileAddress = new ProfileAddress();
//        $profileAddress->setCountry($manager->merge($this->getReference('countryAT')));
//        $profileAddress->setProfile($profile);
//        
//        $manager->persist($profileAddress);
//        $manager->persist($profile);
        
        $em = $this->container->get('doctrine.orm.entity_manager');
        
        $ref = 'user-1';
        $profile = new Profile();
        $profile->setFirstName('Alex');
        $profile->setLastName('Bomba');
        $profile->setAbout('This and that');
        $profile->setPhone('004444444444');
//        $profile->setPicture('alex_1.png');
        $profile->setCreated(new \DateTime());
        $profile->setUpdated($profile->getCreated());
        $profile->setUser($manager->merge($this->getReference($ref)));
        if (!$this->isNotificationUser($notificationUsers, $ref)){
            $this->setAllNotificationSettings($profile->getNotificationSettings(), false);
        }
        
        $profileAddress = new ProfileAddress();
        $profileAddress->setAddressLine1('Im Bräunlesrot 20');
        $profileAddress->setLocality('Waldbrunn');
        $profileAddress->setPostcode('69429');
        $profileAddress->setCountry($em->getRepository('ZizooAddressBundle:Country')->findOneByIso('DE'));
        $profileAddress->setProfile($profile);
        
        $profileAddress2 = new ProfileAddress();
        $profileAddress2->setAddressLine1('Brusselsstreet 20');
        $profileAddress2->setLocality('Bruxelles');
        $profileAddress2->setPostcode('1050');
        $profileAddress2->setCountry($em->getRepository('ZizooAddressBundle:Country')->findOneByIso('BE'));
        $profileAddress2->setProfile($profile);
        
        $manager->persist($profileAddress);
        $manager->persist($profileAddress2);
        $manager->persist($profile);
        
        $this->addReference('profile-1', $profile);
        
        $ref = 'user-2';
        $profile = new Profile();
        $profile->setFirstName('Benito');
        $profile->setLastName('Gonzo');
        $profile->setAbout('If I am not me, then who de hell am I');
        $profile->setPhone('006666666');
        $profile->setCreated(new \DateTime());
        $profile->setUpdated($profile->getCreated());
        $profile->setUser($manager->merge($this->getReference($ref)));
        if (!$this->isNotificationUser($notificationUsers, $ref)){
            $this->setAllNotificationSettings($profile->getNotificationSettings(), false);
        }
        
        $profileAddress = new ProfileAddress();
        $profileAddress->setAddressLine1('Wienerstr. 80');
        $profileAddress->setLocality('Wien');
        $profileAddress->setPostcode('77777');
        $profileAddress->setCountry($em->getRepository('ZizooAddressBundle:Country')->findOneByIso('AT'));
        $profileAddress->setProfile($profile);
        
        $manager->persist($profileAddress);
        $manager->persist($profile);
        
        $this->addReference('profile-2', $profile);
        
        
        $ref = 'user-3';
        $profile = new Profile();
        $profile->setFirstName('Sinan');
        $profile->setLastName('Masovic');
        $profile->setCreated(new \DateTime());
        $profile->setUpdated($profile->getCreated());
        $profile->setUser($manager->merge($this->getReference($ref)));
        if (!$this->isNotificationUser($notificationUsers, $ref)){
            $this->setAllNotificationSettings($profile->getNotificationSettings(), false);
        }
        
        $profileAddress = new ProfileAddress();
        
        $profileAddress->setLocality('Wien');
        $profileAddress->setCountry($em->getRepository('ZizooAddressBundle:Country')->findOneByIso('AT'));
        $profileAddress->setProfile($profile);
        
        $manager->persist($profileAddress);
        $manager->persist($profile);
        
        $this->addReference('profile-3', $profile);
        
        
        $manager->flush();

        $this->initialisePaymentCustomers();
    }

    public function getOrder()
    {
        return 3;
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