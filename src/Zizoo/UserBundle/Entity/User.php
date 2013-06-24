<?php
// src/Zizoo/UserBundle/Entity/User.php
namespace Zizoo\UserBundle\Entity;

use Zizoo\BaseBundle\Entity\BaseEntity;

use FOS\MessageBundle\Model\ParticipantInterface;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Zizoo\UserBundle\Entity\User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Zizoo\UserBundle\Entity\UserRepository")
 * @UniqueEntity(fields="username", groups={"registration"}, message="zizoo_user.error.user_taken")
 * @UniqueEntity(fields="email", groups={"registration"}, message="zizoo_user.error.email_taken")
 */
class User extends BaseEntity implements AdvancedUserInterface, \Serializable, ParticipantInterface
{
    
    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;
    
    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;
    
    /**
     * @ORM\Column(type="string", length=60, unique=false, nullable=true)
     */
    private $newEmail;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @ORM\Column(name="confirmation_token", type="string", length=255, nullable=true)
     */
    private $confirmationToken;
    
    /**
     * @ORM\Column(name="change_email_token", type="string", length=255, nullable=true)
     */
    private $changeEmailToken;
    
    /**
     * @ORM\Column(name="fb_uid", type="string", length=255, nullable=true)
     */
    private $facebookUID;
     
    /**
     * @ORM\OneToMany(targetEntity="Zizoo\MessageBundle\Entity\Contact", mappedBy="sender")
     */
    private $myContacts;

    /**
     * @ORM\OneToMany(targetEntity="Zizoo\MessageBundle\Entity\Contact", mappedBy="recipient")
     */
    private $contactsWithMe;
    
    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     *
     */
    private $groups;
        
    /**
     * @ORM\OneToOne(targetEntity="\Zizoo\ProfileBundle\Entity\Profile", mappedBy="user")
     **/
    private $profile;
    
    /**
     * @ORM\OneToMany(targetEntity="\Zizoo\CrewBundle\Entity\Skills", mappedBy="user", cascade={"persist"})
     **/
    private $skills;
    
    /**
    * @ORM\ManyToMany(targetEntity="\Zizoo\CharterBundle\Entity\Charter", mappedBy="users")
    */
    protected $charter;
    
    /**
     * @ORM\OneToMany(targetEntity="Zizoo\ReservationBundle\Entity\Reservation", mappedBy="guest")
     */
    private $reservations;
          
    /**
     * @ORM\OneToMany(targetEntity="Zizoo\BookingBundle\Entity\Booking", mappedBy="renter")
     */
    private $bookings;
    
    
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }
    
    
    public function __construct()
    {
        parent::__construct();
        $this->isActive = false;
        $this->groups = new ArrayCollection();
        $this->charter = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->skills = new ArrayCollection();
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    public function getRoles()
    {
        return $this->groups->toArray();
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
        ) = unserialize($serialized);
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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }
        
    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setNewEmail($newEmail)
    {
        $this->newEmail = $newEmail;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getNewEmail()
    {
        return $this->newEmail;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add groups
     *
     * @param \Zizoo\UserBundle\Entity\Group $groups
     * @return User
     */
    public function addGroup(\Zizoo\UserBundle\Entity\Group $groups)
    {
        $this->groups[] = $groups;
    
        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Zizoo\UserBundle\Entity\Group $groups
     */
    public function removeGroup(\Zizoo\UserBundle\Entity\Group $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set confirmationToken
     *
     * @param string $confirmationToken
     * @return User
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
    
        return $this;
    }

    /**
     * Get confirmationToken
     *
     * @return string 
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }
    
    /**
     * Set changeEmailToken
     *
     * @param string $changeEmailToken
     * @return User
     */
    public function setChangeEmailToken($changeEmailToken)
    {
        $this->changeEmailToken = $changeEmailToken;
    
        return $this;
    }

    /**
     * Get changeEmailToken
     *
     * @return string 
     */
    public function getChangeEmailToken()
    {
        return $this->changeEmailToken;
    }
    
   

    /**
     * Set profile
     *
     * @param \Zizoo\ProfileBundle\Entity\Profile $profile
     * @return User
     */
    public function setProfile(\Zizoo\ProfileBundle\Entity\Profile $profile = null)
    {
        $this->profile = $profile;
    
        return $this;
    }

    /**
     * Get profile
     *
     * @return \Zizoo\ProfileBundle\Entity\Profile 
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set facebookUID
     *
     * @param string $facebookUID
     * @return User
     */
    public function setFacebookUID($facebookUID)
    {
        $this->facebookUID = $facebookUID;
    
        return $this;
    }

    /**
     * Get facebookUID
     *
     * @return string 
     */
    public function getFacebookUID()
    {
        return $this->facebookUID;
    }
    
    public function __toString(){
        return '' . $this->username . '';
    }

    /**
     * Add myContacts
     *
     * @param \Zizoo\MessageBundle\Entity\Contact $myContacts
     * @return User
     */
    public function addMyContact(\Zizoo\MessageBundle\Entity\Contact $myContacts)
    {
        $this->myContacts[] = $myContacts;
    
        return $this;
    }

    /**
     * Remove myContacts
     *
     * @param \Zizoo\MessageBundle\Entity\Contact $myContacts
     */
    public function removeMyContact(\Zizoo\MessageBundle\Entity\Contact $myContacts)
    {
        $this->myContacts->removeElement($myContacts);
    }

    /**
     * Get myContacts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMyContacts()
    {
        return $this->myContacts;
    }

    /**
     * Add contactsWithMe
     *
     * @param \Zizoo\MessageBundle\Entity\Contact $contactsWithMe
     * @return User
     */
    public function addContactsWithMe(\Zizoo\MessageBundle\Entity\Contact $contactsWithMe)
    {
        $this->contactsWithMe[] = $contactsWithMe;
    
        return $this;
    }

    /**
     * Remove contactsWithMe
     *
     * @param \Zizoo\MessageBundle\Entity\Contact $contactsWithMe
     */
    public function removeContactsWithMe(\Zizoo\MessageBundle\Entity\Contact $contactsWithMe)
    {
        $this->contactsWithMe->removeElement($contactsWithMe);
    }

    /**
     * Get contactsWithMe
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContactsWithMe()
    {
        return $this->contactsWithMe;
    }
    
    /**
     * Set charter
     * 
     * @param \Zizoo\UserBundle\Entity\Charter $charter
     * @return \Zizoo\UserBundle\Entity\User
     */
    
    public function setCharter(\Zizoo\CharterBundle\Entity\Charter $charter)
    {
        $this->charter = new ArrayCollection(array($charter));
        return $this;
    }
    
    /**
     * Get charter
     *
     * @return \Zizoo\CharterBundle\Entity\Charter $charter
     */
    public function getCharter() {
        return $this->charter->first();
    }
    
    /**
     * Add reservations
     *
     * @param \Zizoo\ReservationBundle\Entity\Reservation $reservations
     * @return Charter
     */
    public function addReservation(\Zizoo\ReservationBundle\Entity\Reservation $reservations)
    {
        $this->reservations[] = $reservations;
    
        return $this;
    }

    /**
     * Remove reservations
     *
     * @param \Zizoo\ReservationBundle\Entity\Reservation $reservations
     */
    public function removeReservation(\Zizoo\ReservationBundle\Entity\Reservation $reservations)
    {
        $this->reservations->removeElement($reservations);
    }

    /**
     * Get reservations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    /**
     * Add bookings
     *
     * @param \Zizoo\BookingBundle\Entity\Booking $bookings
     * @return Charter
     */
    public function addBooking(\Zizoo\BookingBundle\Entity\Booking $bookings)
    {
        $this->bookings[] = $bookings;
    
        return $this;
    }

    /**
     * Remove bookings
     *
     * @param \Zizoo\BookingBundle\Entity\Booking $bookings
     */
    public function removeBooking(\Zizoo\BookingBundle\Entity\Booking $bookings)
    {
        $this->bookings->removeElement($bookings);
    }

    /**
     * Get bookings
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBookings()
    {
        return $this->bookings;
    }

    /**
     * Add skill
     *
     * @param \Zizoo\CrewBundle\Entity\Skills $skill
     * @return User
     */
    public function addSkill(\Zizoo\CrewBundle\Entity\Skills $skill)
    {
        $skill->setUser($this);

        $this->skills->add($skill);

        return $this;
    }

    /**
     * Remove skill
     *
     * @param \Zizoo\CrewBundle\Entity\Skills $skill
     */
    public function removeSkill(\Zizoo\CrewBundle\Entity\Skills $skill)
    {
        $this->skills->removeElement($skill);
    }

    /**
     * Get skills
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSkills()
    {
        return $this->skills;
    }

}