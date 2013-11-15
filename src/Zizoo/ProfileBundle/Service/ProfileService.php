<?php
namespace Zizoo\ProfileBundle\Service;

use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\ProfileBundle\Entity\ProfileAvatar;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;

class ProfileService {

    const MAX_PROFILE_COMPLETENESS = 3;
    
    private $em;
    private $container;

    public function __construct(EntityManager $em, Container $container) {
        $this->em = $em;
        $this->container = $container;
    }

   public function getCompleteness(Profile $profile)
    {
        $completeness = 0;
        if($profile->getFirstName()&&$profile->getLastName()&&$profile->getAddress()&&$profile->getPhone()){
            $completeness++;
        }
        $profileAvatars = $profile->getAvatar();
        if($profileAvatars->count()>0&&$profile->getAbout()&&$profile->getLanguages()){
            $completeness++;
        }
        //Verification
//        if($profile->faceBookVerified()&&$profile->twitterVerified()&&$profile->phoneVerified()){
//            $completeness++;
//        }
        return $completeness;
    }
    
    public function addAvatars(Profile $profile, $imageFiles, $flush=true){
        $profileAvatars = new ArrayCollection();
        foreach ($imageFiles as $imageFile){
            $profileAvatars->add($this->addAvatar($profile, $imageFile, false));
        }
        if ($flush) $this->em->flush();
        return $profileAvatars;
    }
    
    public function addAvatar(Profile $profile, $imageFile, $flush=true){
        
        if (!$imageFile instanceof UploadedFile){
            throw new \Exception('Unable to upload');
        }

        $avatar = new ProfileAvatar();
        $avatar->setFile($imageFile);
        $avatar->setPath($imageFile->guessExtension());
        $avatar->setMimeType($imageFile->getMimeType());
        $avatar->setOriginalFilename($imageFile->getClientOriginalName());
        $avatar->setProfile($profile);
        $profile->addAvatar($avatar);
        
        $validator          = $this->container->get('validator');
        $profileErrors      = $validator->validate($profile, 'avatars');
        $avatarErrors       = $validator->validate($avatar, 'avatars');
        $numProfileErrors   = $profileErrors->count();
        $numAvatarErrors    = $avatarErrors->count();
        
        if ($numProfileErrors==0 && $numAvatarErrors==0){
            
            $profile->setUpdated(new \DateTime());

            try {
                $this->em->persist($avatar);
                
                if ($flush) $this->em->flush();
                
                return $avatar;
                
            } catch (\Exception $e){
                throw new \Exception('Unable to upload: ' . $e->getMessage());
            }
            
        } else {
            
            $errorArr = array();
            for ($i=0; $i<$numProfileErrors; $i++){
                $error = $profileErrors->get($i);
                $msgTemplate = $error->getMessage();
                $errorArr[] = $msgTemplate;
            }
            for ($i=0; $i<$numAvatarErrors; $i++){
                $error = $avatarErrors->get($i);
                $msgTemplate = $error->getMessage();
                $errorArr[] = $msgTemplate;
            }
            
            throw new \Exception(join(',', $errorArr));
            
        }
             
    }
}
?>
