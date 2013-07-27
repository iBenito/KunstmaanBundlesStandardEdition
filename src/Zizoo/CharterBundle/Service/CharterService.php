<?php
namespace Zizoo\CharterBundle\Service;

use Zizoo\CharterBundle\Entity\Charter;
use Zizoo\CharterBundle\Entity\CharterLogo;
use Zizoo\UserBundle\Entity\User;

use Symfony\Component\HttpFoundation\File\UploadedFile;

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

    public function getCompleteness(Charter $charter)
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
    
    public function setCharterLogo(Charter $charter, $imageFile, $flush=true){
        
        if (!$imageFile instanceof UploadedFile){
            throw new \Exception('Unable to upload');
        }

        $oldLogo = $charter->getLogo();
            
        $logo = new CharterLogo();
        $logo->setFile($imageFile);
        $logo->setPath($imageFile->guessExtension());
        $logo->setMimeType($imageFile->getMimeType());
        
        $validator          = $this->container->get('validator');
        $charterErrors      = $validator->validate($charter, 'logo');
        $logoErrors         = $validator->validate($logo, 'logo');
        $numCharterErrors   = $charterErrors->count();
        $numLogoErrors      = $logoErrors->count();
        
        if ($numCharterErrors==0 && $numLogoErrors==0){
            
            $logo->setCharter($charter);
            $charter->setLogo($logo);
            $charter->setUpdated(new \DateTime());

            try {
                $this->em->persist($logo);
                
                // Remove old logo
                if ($oldLogo) $this->em->remove($oldLogo);
                
                if ($flush) $this->em->flush();
                
                return $logo;
                
            } catch (\Exception $e){
                throw new \Exception('Unable to upload: ' . $e->getMessage());
            }
            
        } else {
            
            $errorArr = array();
            for ($i=0; $i<$numCharterErrors; $i++){
                $error = $charterErrors->get($i);
                $msgTemplate = $error->getMessage();
                $errorArr[] = $msgTemplate;
            }
            for ($i=0; $i<$numLogoErrors; $i++){
                $error = $logoErrors->get($i);
                $msgTemplate = $error->getMessage();
                $errorArr[] = $msgTemplate;
            }
            
            throw new \Exception(join(',', $errorArr));
            
        }
             
    }

}


?>
