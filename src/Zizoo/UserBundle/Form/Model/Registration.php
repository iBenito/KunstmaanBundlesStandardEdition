<?php
// src/Zizoo/UserBundle/Form/Model/Registration.php
namespace Zizoo\UserBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use Zizoo\UserBundle\Entity\User;
use Zizoo\ProfileBundle\Entity\Profile;

class Registration
{
    /**
     * @Assert\Type(type="Zizoo\UserBundle\Entity\User")
     */
    protected $user;
    
    /**
     * @Assert\Type(type="Zizoo\ProfileBundle\Entity\Profile")
     */
    protected $profile;

    /**
     * @Assert\True(message="You must accept the Terms & Conditions.")
     */
    protected $termsAccepted;

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
    
    public function setProfile(Profile $profile)
    {
        $this->profile = $profile;
    }

    public function getProfile()
    {
        return $this->profile;
    }

    public function getTermsAccepted()
    {
        return $this->termsAccepted;
    }

    public function setTermsAccepted($termsAccepted)
    {
        $this->termsAccepted = (boolean)$termsAccepted;
    }
}
?>