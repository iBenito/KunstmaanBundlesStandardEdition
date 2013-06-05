<?php

namespace Zizoo\UserBundle\Form\Model;

class AccountSettings
{
    protected $password;
    protected $newPassword;
    protected $newEmail;
    
    public function __construct() 
    {
        
    }
    
    public function getPassword()
    {
        return $this->password;
    }
    
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }
    
    public function getNewPassword()
    {
        return $this->newPassword;
    }
    
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;
        return $this;
    }
    
    public function getNewEmail()
    {
        return $this->newEmail;
    }
    
    public function setNewEmail($newEmail)
    {
        $this->newEmail = $newEmail;
        return $this;
    }
    
}
