<?php
// src/Zizoo/UserBundle/Form/Model/UserNewPassword.php
namespace Zizoo\UserBundle\Form\Model;

class UserNewPassword
{
    protected $password;

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

}
?>