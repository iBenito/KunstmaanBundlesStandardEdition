<?php
// src/Zizoo/UserBundle/Form/Model/Invite.php
namespace Zizoo\UserBundle\Form\Model;

use Doctrine\Common\Collections\ArrayCollection;

class Invite
{
    protected $emails;
    
    public function __construct()
    {
        $this->emails = new ArrayCollection();
    }
    
    public function setEmails($emails){
        $this->emails = $emails;
        return $this;
    }
    
    public function getEmails(){
        return $this->emails;
    }
    
    public function addEmail($email)
    {
        $this->emails->add($email);
    }
}

class InviteSingle
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
}
?>