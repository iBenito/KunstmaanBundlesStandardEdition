<?php

namespace Zizoo\MessageBundle\Entity;

use Zizoo\BookingBundle\Entity\Booking;

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
    
    
    /**
     * @ORM\OneToOne(targetEntity="Zizoo\BookingBundle\Entity\Booking", inversedBy="thread")
     * @ORM\JoinColumn(name="booking_id", referencedColumnName="id")
     **/
    protected $booking;
    
    

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
    
    public function setBooking(Booking $booking)
    {
        $this->booking = $booking;
        return $this;
    }
    
    public function getBooking()
    {
        return $this->booking;
    }
    
    public function getMessages()
    {
        return $this->messages->toArray();
    }
    
    public function getMessagesReverse()
    {
        return array_reverse($this->messages->toArray());
    }
    
    public function getLastMessageType()
    {
        $lastMessageType = null;
        foreach ($this->messages as $message){
            if ($message->getMessageType()) $lastMessageType = $message->getMessageType();
        }
        return $lastMessageType;
    }

}