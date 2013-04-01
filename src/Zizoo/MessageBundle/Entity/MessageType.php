<?php

namespace Zizoo\MessageBundle\Entity;

use Zizoo\MessageBundle\Entity\MessageRecipient;
use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="message_type")
 * @ORM\Entity(repositoryClass="Zizoo\MessageBundle\Entity\MessageTypeRepository")
 */
class MessageType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="string", length=255)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="message_type", type="string", length=255)
     */
    private $name;

    
    public function __construct($id=null, $name=null){
        $this->id   = $id;
        $this->name = $name;
    }
    
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }
    
    public function setName($name){
        $this->name = $name;
    }
}