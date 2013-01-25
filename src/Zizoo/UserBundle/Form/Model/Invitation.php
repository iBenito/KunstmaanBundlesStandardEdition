<?php
// src/Zizoo/UserBundle/Form/Model/Invitation.php
namespace Zizoo\UserBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use Zizoo\UserBundle\Entity\User;

class Invitation
{

    protected $email1;
    protected $email2;
    protected $email3;
    protected $email4;
    protected $email5;
    
    public function setEmail1($email){
        $this->email1 = $email;
        return $this;
    }
    
    public function getEmail1(){
        return $this->email1;
    }
    
    public function setEmail2($email){
        $this->email2 = $email;
        return $this;
    }
    
    public function getEmail2(){
        return $this->email2;
    }
    
    public function setEmail3($email){
        $this->email3 = $email;
        return $this;
    }
    
    public function getEmail3(){
        return $this->email3;
    }
    
    public function setEmail4($email){
        $this->email4 = $email;
        return $this;
    }
    
    public function getEmail4(){
        return $this->email4;
    }
    
    public function setEmail5($email){
        $this->email5 = $email;
        return $this;
    }
    
    public function getEmail5(){
        return $this->email5;
    }
}
?>