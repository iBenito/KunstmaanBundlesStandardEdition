<?php

namespace Zizoo\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use FOS\MessageBundle\Entity\Thread as BaseThread;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\Model\ThreadMetadata as ModelThreadMetadata;

/**
 * @ORM\Entity
 * @ORM\Table(name="message_thread")
 */
class Thread extends BaseThread
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\UserBundle\Entity\User")
     */
    protected $createdBy;

    /**
     * @ORM\OneToMany(targetEntity="Zizoo\MessageBundle\Entity\Message", mappedBy="thread")
     */
    protected $messages;

    /**
     * @ORM\OneToMany(targetEntity="Zizoo\MessageBundle\Entity\ThreadMetadata", mappedBy="thread", cascade={"all"})
     */
    protected $metadata;

    public function __construct()
    {
        parent::__construct();

        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function setCreatedBy(ParticipantInterface $participant) {
            $this->createdBy = $participant;
            return $this;
    }

    function addMessage(MessageInterface $message) {
            $this->messages->add($message);
    }

    public function addMetadata(ModelThreadMetadata $meta) {
        $meta->setThread($this);
        parent::addMetadata($meta);
    }
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\MessageBundle\Entity\ThreadType")
     * @ORM\JoinColumn(name="thread_type", referencedColumnName="id")
     */
    protected $threadType;
    
    /**
     * Set type
     *
     * @param ThreadType $type
     * @return Message
     */
    public function setThreadType($type)
    {
        $this->threadType = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return ThreadType 
     */
    public function getThreadType()
    {
        return $this->threadType;
    }


}