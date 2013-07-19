<?php
namespace Zizoo\CharterBundle\Service;

use Zizoo\CharterBundle\Entity\Charter;
use Zizoo\UserBundle\Entity\User;

use Symfony\Component\DependencyInjection\Container;

use Doctrine\ORM\EntityManager;

class CharterService
{
    const MAX_CHARTER_COMPLETENESS = 3;

    private $em;
    private $container;
    
    public function __construct(EntityManager $em, Container $container) {
        $this->em = $em;
        $this->container = $container;
    }
    
    public function setupCharter(Charter $charter, User $adminUser, User $billingUser, $flush){

        $charter->setAdminUser($adminUser);
        $charter->setBillingUser($billingUser);
        $charter->addUser($adminUser);
        if ($adminUser!=$billingUser){
            $charter->addUser($billingUser);
        }
        
        $adminUser->setCharter($charter);
        $billingUser->setCharter($charter);
        
        $charterAddress = $charter->getAddress();
        $charterAddress->setCharter($charter);
        
        $this->em->persist($charterAddress);
        $this->em->persist($charter);
        $this->em->persist($adminUser);
        $this->em->persist($billingUser);
        
        if ($flush) $this->em->flush();
    }

    public function getCompleteness(\Zizoo\CharterBundle\Entity\Charter $charter)
    {
        $completeness = 0;

        // check completeness against validation rules
        $validator = $this->container->get('validator');
        for($level = 1; $level <= self::MAX_CHARTER_COMPLETENESS; $level++)
        {
            $validationGroup = 'CompletenessLevel'.$level;
            $errors = $validator->validate($charter, array($validationGroup));
            if(!$errors->count()){
                $completeness++;
            }
        }
        //Verification
//        if($profile->faceBookVerified()&&$profile->twitterVerified()&&$profile->phoneVerified()){
//            $completeness++;
//        }
        return $completeness;
    }

}


?>
