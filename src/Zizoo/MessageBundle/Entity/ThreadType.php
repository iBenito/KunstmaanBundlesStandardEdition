<?php

namespace Zizoo\MessageBundle\Entity;

use Zizoo\MessageBundle\Entity\MessageRecipient;
use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="message_thread_type")
 * @ORM\Entity(repositoryClass="Zizoo\MessageBundle\Entity\ThreadTypeRepository")
 */
class ThreadType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="thread_type", type="string", length=255)
     */
    private $name;

    
    public function __construct($name=null){
        $this->name = $name;
    }
    
    
    
    /**
     * Get id
     *
     * @return integer 
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