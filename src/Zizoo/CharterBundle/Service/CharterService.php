<?php
namespace Zizoo\CharterBundle\Service;

use Zizoo\CharterBundle\Entity\Charter;
use Zizoo\UserBundle\Entity\User;

use Symfony\Component\DependencyInjection\Container;

use Doctrine\ORM\EntityManager;

class CharterService
{
    private $em;
    private $container;
    
    public function __construct(EntityManager $em, Container $container) {
        $this->em = $em;
        $this->container = $container;
        
    }
    
    public function createCharter($charterName, $charterNumber, User $adminUser, User $billingUser, $flush){
        $charter = new Charter();
        $charter->setCharterName($charterName);
        $charter->setCharterNumber($charterNumber);
        
        $charter->setAdminUser($adminUser);
        $charter->setBillingUser($billingUser);
        $charter->addUser($adminUser);
        if ($adminUser!=$billingUser){
            $charter->addUser($billingUser);
        }
        
        $adminUser->setCharter($charter);
        $billingUser->setCharter($charter);
        
        $this->em->persist($charter);
        $this->em->persist($adminUser);
        $this->em->persist($billingUser);
        
        if ($flush) $this->em->flush();
    }
    
    
}


?>
