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
use Doctrine\Common\Collections\ArrayCollection;

use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\MessageBundle\Entity\Message;
use Zizoo\MessageBundle\Entity\MessageRecipient;
use Zizoo\MessageBundle\Service\Messenger;

class MessageFixtures implements OrderedFixtureInterface, SharedFixtureInterface, ContainerAwareInterface
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
    }
    
    public function load(ObjectManager $manager)
    {
        $profile1 = $this->getReference('profile-1');
        $profile2 = $this->getReference('profile-2');
        $profile3 = $this->getReference('profile-3');
        
        $messenger = $this->container->get('messenger');
        
        // Message thread 1 from profile 2 (Benny) to profile 1 (Alex)
        $message = $messenger->sendMessageTo($profile2, $profile1, 'This is a test', 'First message!');
        
        // Reply (thread 1) from profile 1 (Alex) to profile 2 (Benny), CC profile 3 (Sinan)
        $recipients = new ArrayCollection();
        $recipients->add($profile3);
        $message2 = $messenger->sendMessage($profile1, $recipients, "It's working!! I'm copying Sinan in :-)", null, $message);
                
        // Reply (thread 1) from profile 2 (Benny) to profile 1 (Alex) CC profile 3 (Sinan)
        $recipients = new ArrayCollection();
        $recipients->add($profile3);
        $message3 = $messenger->sendMessage($profile2, $recipients, "Awesome!", null, $message2);
        
        
        // Message thread 2 from profile 1 (Alex) to profile 2 (Benny), CC profile 3 (Sinan)
        $recipients = new ArrayCollection();
        $recipients->add($profile2);
        $recipients->add($profile3);
        $message4 = $messenger->sendMessage($profile1, $recipients, "Awesome!", '2nd thread');

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