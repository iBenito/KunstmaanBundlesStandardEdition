<?php
namespace Zizoo\BillingBundle\Form\Model;

class PayPal {
 
    protected $username;
    
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
    
    public function getUsername()
    {
        return $this->username;
    }
    
}
?>
