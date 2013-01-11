<?php
// src/Zizoo/UserBundle/DataFixtures/ORM/UserFixtures.php

namespace ZizoozUserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zizoo\UserBundle\Entity\Group;
use Zizoo\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class UserFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $encoder1 = new MessageDigestPasswordEncoder('sha512', true, 10);
        $user_1 = new User();
        $user_1->setUsername('alexf');
        $user_1->setEmail('alexf83@gmail.com');
        $user_1->setSalt(md5(time()));
        $password1 = $encoder1->encodePassword('hahaha', $user_1->getSalt());
        $user_1->setPassword($password1);
        $user_1->setIsActive(true);
        $user_1->addGroup($manager->merge($this->getReference('group_admin')));
       
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
        $user_2->addGroup($manager->merge($this->getReference('group_admin')));
        
        $this->addReference('user-2', $user_2);
        
        
        $encoder3 = new MessageDigestPasswordEncoder('sha512', true, 10);
        $user_3 = new User();
        $user_3->setUsername('skippa');
        $user_3->setEmail('sinan.masovic@gmail.com');
        $user_3->setSalt(md5(time()));
        $password3 = $encoder3->encodePassword('sinan', $user_3->getSalt());
        $user_3->setPassword($password3);
        $user_3->setIsActive(true);
        $user_3->addGroup($manager->merge($this->getReference('group_user')));
        
        $this->addReference('user-3', $user_3);
        
        
        $encoder4 = new MessageDigestPasswordEncoder('sha512', true, 10);
        $user_4 = new User();
        $user_4->setUsername('tilentravnik');
        $user_4->setEmail('tilen.travnik@dlabs.si');
        $user_4->setSalt('85b2814b88930894720b636feba55968');
        $user_4->setPassword('14kBeIa/vkdxJeFWMi25uyXfbpdRpnABDZQQuxdwdTlkbjuBZTzFld4i9vGMfyR/akT2wXeIDSxqjVTmkKO3Vg==');
        $user_4->setIsActive(true);
        $user_4->addGroup($manager->merge($this->getReference('group_user')));
        
        $this->addReference('user-4', $user_4);
        
        $manager->persist($user_1);
        $manager->persist($user_2);
        $manager->persist($user_3);

        $manager->flush();
  
    }
    
    public function getOrder()
    {
        return 2;
    }

}
?>