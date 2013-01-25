<?php

namespace Zizoo\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\MessageBundle\Model\MessageMetadata as ModelMessageMetadata;

use FOS\MessageBundle\Entity\Message as BaseMessage;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\MessageMetadata as ModelMessageMetadata;

/**
 * @ORM\Entity
 * @ORM\Table(name="message_message")
 */
class Message extends BaseMessage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\MessageBundle\Entity\Thread", inversedBy="messages")
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id")
     */
    protected $thread;

    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $sender;

    /**
     * @ORM\OneToMany(targetEntity="Zizoo\MessageBundle\Entity\MessageMetadata", mappedBy="message", cascade={"all"})
     */
    protected $metadata;

    public function __construct()
    {
        parent::__construct();

        $this->metadata  = new ArrayCollection();
    }

    public function setThread(ThreadInterface $thread) {
            $this->thread = $thread;
            return $this;
    }

    public function setSender(ParticipantInterface $sender) {
            $this->sender = $sender;
            return $this;
    }

    public function addMetadata(ModelMessageMetadata $meta) {
        $meta->setMessage($this);
        parent::addMetadata($meta);
    }

}