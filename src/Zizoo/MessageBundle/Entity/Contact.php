<?php

namespace Zizoo\MessageBundle\Entity;

use Zizoo\UserBundle\Entity\User;

use FOS\MessageBundle\Model\ThreadInterface;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="message_contact")
 */
class Contact 
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     */
    protected $sender;

    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="recipient_id", referencedColumnName="id")
     */
    protected $recipient;
    
    
    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;
    
    
    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="blocked_at", type="datetime", nullable=true)
     */
    protected $blockedAt;

    
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function setThread(ThreadInterface $thread) {
            $this->thread = $thread;
            return $this;
    }

    public function setSender(User $sender) {
            $this->sender = $sender;
            return $this;
    }

    public function addMetadata(ModelMessageMetadata $meta) {
        $meta->setMessage($this);
        parent::addMetadata($meta);
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Contact
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set blockedAt
     *
     * @param \DateTime $blockedAt
     * @return Contact
     */
    public function setBlockedAt($blockedAt)
    {
        $this->blockedAt = $blockedAt;
    
        return $this;
    }

    /**
     * Get blockedAt
     *
     * @return \DateTime 
     */
    public function getBlockedAt()
    {
        return $this->blockedAt;
    }

    /**
     * Get sender
     *
     * @return \Zizoo\UserBundle\Entity\User 
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set recipient
     *
     * @param \Zizoo\UserBundle\Entity\User $recipient
     * @return Contact
     */
    public function setRecipient(\Zizoo\UserBundle\Entity\User $recipient = null)
    {
        $this->recipient = $recipient;
    
        return $this;
    }

    /**
     * Get recipient
     *
     * @return \Zizoo\UserBundle\Entity\User 
     */
    public function getRecipient()
    {
        return $this->recipient;
    }
}