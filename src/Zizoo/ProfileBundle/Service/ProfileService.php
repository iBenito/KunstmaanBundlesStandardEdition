<?php
namespace Zizoo\ProfileBundle\Service;

use Zizoo\ProfileBundle\Entity\Profile;

class ProfileService {

    private $em;

    public function __construct($em) {
        $this->em = $em;
    }

   public function getCompleteness(\Zizoo\ProfileBundle\Entity\Profile $profile)
    {
        $completeness = 0;
        if($profile->getFirstName()&&$profile->getLastName()&&$profile->getAddress()&&$profile->getPhone()){
            $completeness++;
        }
        if($profile->getAvatar()&&$profile->getAbout()&&$profile->getLanguages()){
            $completeness++;
        }
        //Verification
//        if($profile->faceBookVerified()&&$profile->twitterVerified()&&$profile->phoneVerified()){
//            $completeness++;
//        }
        return $completeness;
    }
}
?>
