<?php

namespace Zizoo\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MessageRecipient
 *
 * @ORM\Table(name="message_recipient")
 * @ORM\Entity(repositoryClass="Zizoo\MessageBundle\Entity\MessageRecipientRepository")
 */
class MessageRecipient
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
     * @ORM\ManyToOne(targetEntity="Zizoo\ProfileBundle\Entity\Profile", inversedBy="incoming_messages")
     */
    private $recipient_profile;

    /**
     * @var boolean
     *
     * @ORM\Column(name="recipient_keep", type="boolean")
     */
    private $recipient_keep;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="recipient_read_date", type="datetime", nullable=true)
     */
    private $recipient_read_date;

    
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\MessageBundle\Entity\Message", inversedBy="recipients")
     */
    private $message;

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setRecipientKeep(true);
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


    /**
     * Set recipient_keep
     *
     * @param boolean $recipientKeep
     * @return MessageRecipient
     */
    public function setRecipientKeep($recipientKeep)
    {
        $this->recipient_keep = $recipientKeep;
    
        return $this;
    }

    /**
     * Get recipient_keep
     *
     * @return boolean 
     */
    public function getRecipientKeep()
    {
        return $this->recipient_keep;
    }

    /**
     * Set recipient_read_date
     *
     * @param \DateTime $recipientReadDate
     * @return MessageRecipient
     */
    public function setRecipientReadDate($recipientReadDate)
    {
        $this->recipient_read_date = $recipientReadDate;
    
        return $this;
    }

    /**
     * Get recipient_read_date
     *
     * @return \DateTime 
     */
    public function getRecipientReadDate()
    {
        return $this->recipient_read_date;
    }

    /**
     * Set recipient_profile
     *
     * @param \Zizoo\ProfileBundle\Entity\Profile $recipientProfile
     * @return MessageRecipient
     */
    public function setRecipientProfile(\Zizoo\ProfileBundle\Entity\Profile $recipientProfile = null)
    {
        $this->recipient_profile = $recipientProfile;
    
        return $this;
    }

    /**
     * Get recipient_profile
     *
     * @return \Zizoo\ProfileBundle\Entity\Profile 
     */
    public function getRecipientProfile()
    {
        return $this->recipient_profile;
    }

    /**
     * Set message
     *
     * @param \Zizoo\MessageBundle\Entity\Message $message
     * @return MessageRecipient
     */
    public function setMessage(\Zizoo\MessageBundle\Entity\Message $message = null)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return \Zizoo\MessageBundle\Entity\Message 
     */
    public function getMessage()
    {
        return $this->message;
    }
}