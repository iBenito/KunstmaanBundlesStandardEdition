<?php
// src/Zizoo/UserBundle/DataFixtures/ORM/UserFixtures.php

namespace ZizoozUserBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\SharedFixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;

use Zizoo\UserBundle\Entity\Group;
use Zizoo\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class UserFixtures implements OrderedFixtureInterface, SharedFixtureInterface, ContainerAwareInterface
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
//        
//        $encoderRegister = new MessageDigestPasswordEncoder('sha512', true, 10);
//        $user_register = new User();
//        $user_register->setUsername('zizoo_registration');
//        $user_register->setEmail($this->container->getParameter('email_register'));
//        $user_register->setSalt(md5(time()));
//        $password_register = $encoderRegister->encodePassword('seaman', $user_register->getSalt());
//        $user_register->setPassword($password_register);
//        $user_register->setIsActive(true);
//        $user_register->addGroup($manager->merge($this->getReference('group_super_admin')));
//       
//        $this->addReference('user-register', $user_register);
//        
//        
//        
//        $encoderInfo = new MessageDigestPasswordEncoder('sha512', true, 10);
//        $user_info = new User();
//        $user_info->setUsername('zizoo_info');
//        $user_info->setEmail($this->container->getParameter('email_info'));
//        $user_info->setSalt(md5(time()));
//        $password_info = $encoderInfo->encodePassword('seaman', $user_info->getSalt());
//        $user_info->setPassword($password_info);
//        $user_info->setIsActive(true);
//        $user_info->addGroup($manager->merge($this->getReference('group_super_admin')));
//       
//        $this->addReference('user-info', $user_info);
        
        $em = $this->container->get('doctrine.orm.entity_manager');
        
        $group_super_admin = $em->getRepository('ZizooUserBundle:Group')->findOneById('ROLE_ZIZOO_SUPER_ADMIN');
        $group_user = $em->getRepository('ZizooUserBundle:Group')->findOneById('ROLE_ZIZOO_USER');
        
        $encoder1 = new MessageDigestPasswordEncoder('sha512', true, 10);
        $user_1 = new User();
        $user_1->setUsername('alexf');
        $user_1->setEmail('alexf83@gmail.com');
        $user_1->setSalt(md5(time()));
        $password1 = $encoder1->encodePassword('hahaha', $user_1->getSalt());
        $user_1->setPassword($password1);
        $user_1->setIsActive(true);
        $user_1->addGroup($group_super_admin);
       
        $this->addReference('user-1', $user_1);
        
        
        $encoder2 = new MessageDigestPasswordEncoder('sha512', true, 10);
        $user_2 = new User();
        $user_2->setUsername('benny');
        $user_2->setEmail('vbenitogo@hotmail.com');
        $user_2->setSalt(md5(time()));
        $password2 = $encoder2->encodePassword('benny', $user_2->getSalt());
        $user_2->setPassword($password2);
        $user_2->setIsActive(true);
        $user_2->setFacebookUID('733240253');
        $user_2->addGroup($group_super_admin);
        
        $this->addReference('user-2', $user_2);
        
        
        $encoder3 = new MessageDigestPasswordEncoder('sha512', true, 10);
        $user_3 = new User();
        $user_3->setUsername('skippa');
        $user_3->setEmail('sinan.masovic@gmail.com');
        $user_3->setSalt(md5(time()));
        $password3 = $encoder3->encodePassword('sinan', $user_3->getSalt());
        $user_3->setPassword($password3);
        $user_3->setIsActive(true);
        $user_3->addGroup($group_user);
        
        $this->addReference('user-3', $user_3);
        
        $manager->persist($user_1);
        $manager->persist($user_2);
        $manager->persist($user_3);

        $manager->flush();
  
    }
    
    public function getOrder()
    {
        return 2;
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
?>