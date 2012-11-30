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
     * @Assert\NotBlank()
     * @Assert\True()
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