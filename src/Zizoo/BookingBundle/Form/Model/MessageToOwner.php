<?php
// src/Zizoo/BookingBundle/Form/Model/MessageToOwner.php
namespace Zizoo\BookingBundle\Form\Model;

class MessageToOwner
{

    protected $subject;
    protected $body;


    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }
    
    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }
}
?>
