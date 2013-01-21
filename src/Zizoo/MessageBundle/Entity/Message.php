<?php

namespace Zizoo\MessageBundle\Entity;

use Zizoo\MessageBundle\Entity\MessageRecipient;
use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="Zizoo\MessageBundle\Entity\MessageRepository")
 */
class Message
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
     * @var \DateTime
     *
     * @ORM\Column(name="sent", type="datetime")
     */
    private $sent;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /**
     * @var Profile
     * 
     * @ORM\ManyToOne(targetEntity="Zizoo\ProfileBundle\Entity\Profile", inversedBy="outgoing_messages")
     */
    private $sender_profile;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sender_keep", type="boolean")
     */
    private $sender_keep;

    
    /**
     * 
     * @ORM\OneToOne(targetEntity="Message")
     * @ORM\JoinColumn(name="reply_to_message", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $reply_to_message;

    /**
     * @var integer
     *
     * @ORM\Column(name="root_message", type="integer", nullable=true)
     */
    private $thread_root_message;

    
    /**
     * @ORM\OneToMany(targetEntity="Zizoo\MessageBundle\Entity\MessageRecipient", mappedBy="message")
     */
    private $recipients;
      
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="message_type", type="integer")
     */
    private $type;
    const MESSAGE   = 0;
    const ENQUIRY   = 1;
    const BOOKING   = 2;
    const BOOKED    = 3;
    const REVIEW    = 4;

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
     * Set sent
     *
     * @param \DateTime $sent
     * @return Message
     */
    public function setSent($sent)
    {
        $this->sent = $sent;
    
        return $this;
    }

    /**
     * Get sent
     *
     * @return \DateTime 
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    
        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Message
     */
    public function setBody($body)
    {
        $this->body = $body;
    
        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set sender_profile
     *
     * @param integer $senderProfile
     * @return Message
     */
    public function setSenderProfile($senderProfile)
    {
        $this->sender_profile = $senderProfile;
    
        return $this;
    }

    /**
     * Get sender_profile
     *
     * @return integer 
     */
    public function getSenderProfile()
    {
        return $this->sender_profile;
    }

    /**
     * Set sender_keep
     *
     * @param boolean $senderKeep
     * @return Message
     */
    public function setSenderKeep($senderKeep)
    {
        $this->sender_keep = $senderKeep;
    
        return $this;
    }

    /**
     * Get sender_keep
     *
     * @return boolean 
     */
    public function getSenderKeep()
    {
        return $this->sender_keep;
    }

    /**
     * Set reply_to_message
     *
     * @param integer $replyToMessage
     * @return Message
     */
    public function setReplyToMessage($replyToMessage)
    {
        $this->reply_to_message = $replyToMessage;
    
        return $this;
    }

    /**
     * Get reply_to_message
     *
     * @return integer 
     */
    public function getReplyToMessage()
    {
        return $this->reply_to_message;
    }

    /**
     * Set thread_root_message
     *
     * @param integer $threadRootMessage
     * @return Message
     */
    public function setThreadRootMessage($threadRootMessage)
    {
        $this->thread_root_message = $threadRootMessage;
    
        return $this;
    }

    /**
     * Get thread_root_message
     *
     * @return integer 
     */
    public function getThreadRootMessage()
    {
        return $this->thread_root_message;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sender_keep = true;
        $this->setSent(new \DateTime());
        $this->recipients = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add recipients
     *
     * @param \Zizoo\MessageBundle\Entity\MessageRecipient $recipients
     * @return Message
     */
    public function addRecipient(\Zizoo\MessageBundle\Entity\MessageRecipient $recipients)
    {
        $this->recipients[] = $recipients;
    
        return $this;
    }

    /**
     * Remove recipients
     *
     * @param \Zizoo\MessageBundle\Entity\MessageRecipient $recipients
     */
    public function removeRecipient(\Zizoo\MessageBundle\Entity\MessageRecipient $recipients)
    {
        $this->recipients->removeElement($recipients);
    }

    /**
     * Get recipients
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecipients()
    {
        return $this->recipients;
    }
    
    public function getReplyMessage($setRecipient=true){
        $reply = new Message();
        $reply->setType($this->getType());
        $reply->setReplyToMessage($this);
        $reply->setSubject('RE: ' . $this->getSubject());
        $rootMessage = $this->getThreadRootMessage();
        if ($rootMessage){
            //$reply->setThreadRootMessage($rootMessage);
        } else {
            //$reply->setThreadRootMessage($this);
        }
        if ($setRecipient){
            $messageRecipient = new MessageRecipient();
            $messageRecipient->setRecipientProfile($this->getSenderProfile());
            $messageRecipient->setMessage($reply);
            $reply->addRecipient($messageRecipient);
        }
        return $reply;
    }
    

    /**
     * Set type
     *
     * @param integer $type
     * @return Message
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }
    
    public function typeToString(){
        switch ($this->type){
            case Message::MESSAGE:
                return 'Message';
                break;
            case Message::ENQUIRY:
                return 'Enquiry';
                break;
            case Message::BOOKING:
                return 'Booking';
                break;
            case Message::BOOKED:
                return 'Booked';
                break;
            case Message::REVIEW:
                return 'Review';
                break;
            default:
                return 'Message';
                break;
        }
    }
    
    public static function getTypeToString($type){
        switch ($type){
            case Message::MESSAGE:
                return 'Message';
                break;
            case Message::ENQUIRY:
                return 'Enquiry';
                break;
            case Message::BOOKING:
                return 'Booking';
                break;
            case Message::BOOKED:
                return 'Booked';
                break;
            case Message::REVIEW:
                return 'Review';
                break;
            default:
                return 'Message';
                break;
        }
    }
}