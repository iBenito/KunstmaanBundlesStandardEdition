<?php
namespace Zizoo\ProfileBundle\Entity;

use FOS\MessageBundle\Model\ParticipantInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Zizoo\ProfileBundle\Entity\ProfileRepository")
 * @ORM\Table(name="profile")
 */
class Profile
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToOne(targetEntity="Zizoo\UserBundle\Entity\User", inversedBy="profile")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $firstName;
        
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $lastName;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $about;

  
    /**
     * @ORM\OneToMany(targetEntity="Zizoo\AddressBundle\Entity\ProfileAddress", mappedBy="profile")
     */
    protected $addresses;
    
    /**
     * @ORM\Column(type="string", length=60, unique=true, nullable=true)
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $picture;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;
    
    
    /**
     * @ORM\OneToMany(targetEntity="Zizoo\MessageBundle\Entity\Message", mappedBy="sender_profile")
     */
    protected $outgoing_messages;
    
    /**
     * @ORM\OneToMany(targetEntity="Zizoo\MessageBundle\Entity\MessageRecipient", mappedBy="recipient_profile")
     */
    protected $incoming_messages;
    
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
     * Set firstName
     *
     * @param string $firstName
     * @return Profile
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Profile
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set about
     *
     * @param string $about
     * @return Profile
     */
    public function setAbout($about)
    {
        $this->about = $about;
    
        return $this;
    }

    /**
     * Get about
     *
     * @return string 
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Profile
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set picture
     *
     * @param string $picture
     * @return Profile
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    
        return $this;
    }

    /**
     * Get picture
     *
     * @return string 
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Profile
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Profile
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set user
     *
     * @param \Zizoo\UserBundle\Entity\User $user
     * @return Profile
     */
    public function setUser(\Zizoo\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Zizoo\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addresses = new ArrayCollection();
    }
    
    /**
     * Add addresses
     *
     * @param \Zizoo\AddressBundle\Entity\ProfileAddress $address
     * @return Profile
     */
    public function addAddresse(\Zizoo\AddressBundle\Entity\ProfileAddress $address)
    {
        $this->addresses[] = $address;
    
        return $this;
    }

    /**
     * Remove addresses
     *
     * @param \Zizoo\AddressBundle\Entity\ProfileAddress $address
     */
    public function removeAddress(\Zizoo\AddressBundle\Entity\ProfileAddress $address)
    {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Remove addresses
     *
     * @param \Zizoo\AddressBundle\Entity\ProfileAddress $addresses
     */
    public function removeAddresse(\Zizoo\AddressBundle\Entity\ProfileAddress $addresses)
    {
        $this->addresses->removeElement($addresses);
    }

    /**
     * Add outgoing_messages
     *
     * @param \Zizoo\MessageBundle\Entity\Message $outgoingMessages
     * @return Profile
     */
    public function addOutgoingMessage(\Zizoo\MessageBundle\Entity\Message $outgoingMessages)
    {
        $this->outgoing_messages[] = $outgoingMessages;
    
        return $this;
    }

    /**
     * Remove outgoing_messages
     *
     * @param \Zizoo\MessageBundle\Entity\Message $outgoingMessages
     */
    public function removeOutgoingMessage(\Zizoo\MessageBundle\Entity\Message $outgoingMessages)
    {
        $this->outgoing_messages->removeElement($outgoingMessages);
    }

    /**
     * Get outgoing_messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOutgoingMessages()
    {
        return $this->outgoing_messages;
    }

    /**
     * Add incoming_messages
     *
     * @param \Zizoo\MessageBundle\Entity\MessageRecipient $incomingMessages
     * @return Profile
     */
    public function addIncomingMessage(\Zizoo\MessageBundle\Entity\MessageRecipient $incomingMessages)
    {
        $this->incoming_messages[] = $incomingMessages;
    
        return $this;
    }

    /**
     * Remove incoming_messages
     *
     * @param \Zizoo\MessageBundle\Entity\MessageRecipient $incomingMessages
     */
    public function removeIncomingMessage(\Zizoo\MessageBundle\Entity\MessageRecipient $incomingMessages)
    {
        $this->incoming_messages->removeElement($incomingMessages);
    }

    /**
     * Get incoming_messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIncomingMessages()
    {
        return $this->incoming_messages;
    }
}