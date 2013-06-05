<?php
// src/Zizoo/UserBundle/Form/Model/UserNewEmail.php
namespace Zizoo\UserBundle\Form\Model;

class UserNewEmail
{
    protected $email;

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }
    
    public function __toString()
    {
        return $this->getEmail();
    }

}
?>