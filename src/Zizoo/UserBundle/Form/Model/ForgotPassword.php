<?php
// src/Zizoo/UserBundle/Form/Model/Registration.php
namespace Zizoo\UserBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use Zizoo\UserBundle\Entity\User;

class Registration
{
    /**
     * @Assert\Type(type="Zizoo\UserBundle\Entity\User")
     */
    protected $user;

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

}
?>