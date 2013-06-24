<?php
namespace Zizoo\ProfileBundle\Entity;
use Zizoo\BaseBundle\Entity\BaseEntity;
use Zizoo\ProfileBundle\Entity\ProfileAvatar;

use Zizoo\ProfileBundle\Entity\Profile\NotificationSettings;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Zizoo\ProfileBundle\Entity\ProfileRepository")
 * @ORM\Table(name="profile")
 * @ORM\HasLifecycleCallbacks
 */
class Profile extends BaseEntity
{
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
     * @ORM\OneToOne(targetEntity="Zizoo\AddressBundle\Entity\ProfileAddress", mappedBy="profile")
     */
    protected $address;
    
    /**
     * @ORM\Column(type="string", length=60, unique=true, nullable=true)
     */
    protected $phone;

    /**
     * @ORM\OneToOne(targetEntity="Zizoo\ProfileBundle\Entity\Profile\NotificationSettings", cascade={"persist"})
     */    
    protected $notification_settings;

    /**
     * @ORM\ManyToMany(targetEntity="\Zizoo\AddressBundle\Entity\Language", inversedBy="profile")
     * @ORM\JoinTable(name="profile_languages",
     *      joinColumns={@ORM\JoinColumn(name="profile_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="language_id", referencedColumnName="language_code")}
     *      )
     **/
    protected $languages;
    
    /**
     * @ORM\OneToMany(targetEntity="Zizoo\ProfileBundle\Entity\ProfileAvatar", mappedBy="profile")
     * @ORM\OrderBy({"order" = "ASC"})
     */
    protected $avatar;
    

    
    public function addAvatar(ProfileAvatar $avatar)
    {
        $this->avatar[] = $avatar;
        return $this;
    }
    
    public function removeAvatar(ProfileAvatar $avatar)
    {
        return $this->avatar->removeElement($avatar);
    }
    
    /*
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('phone', new Assert\Regex(array(
            'pattern' => '/^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$/',
            'message' => 'Please enter a valid Phone Number.'
        )));
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->avatar    = new ArrayCollection();
        $notificationSettings = new NotificationSettings();
        $notificationSettings->setMessage(true);
        $notificationSettings->setEnquiry(true);
        $notificationSettings->setBooked(true);
        $notificationSettings->setBooking(true);
        $notificationSettings->setReview(true);
        $this->setNotificationSettings($notificationSettings);
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
     * Set address
     *
     * @param \Zizoo\AddressBundle\Entity\ProfileAddress $address
     * @return Profile
     */
    public function setAddress(\Zizoo\AddressBundle\Entity\ProfileAddress $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \Zizoo\AddressBundle\Entity\ProfileAddress
     */
    public function getAddress()
    {
        return $this->address;
    }

    
    public function __toString(){
        return '' . $this->user . '';
    }

  

    /**
     * Set notification_settings
     *
     * @param \Zizoo\ProfileBundle\Entity\Profile\NotificationSettings $notificationSettings
     * @return Profile
     */
    public function setNotificationSettings(\Zizoo\ProfileBundle\Entity\Profile\NotificationSettings $notificationSettings = null)
    {
        $this->notification_settings = $notificationSettings;
    
        return $this;
    }

    /**
     * Get notification_settings
     *
     * @return \Zizoo\ProfileBundle\Entity\Profile\NotificationSettings 
     */
    public function getNotificationSettings()
    {
        return $this->notification_settings;
    }

    /**
     * Add language
     *
     * @param \Zizoo\AddressBundle\Entity\Language $language
     * @return Profile
     */
    public function addLanguage(\Zizoo\AddressBundle\Entity\Language $language)
    {
        $this->languages->add($language);

        return $this;
    }

    /**
     * Remove language
     *
     * @param \Zizoo\AddressBundle\Entity\Language $language
     */
    public function removeLanguage(\Zizoo\AddressBundle\Entity\Language $language)
    {
        $this->languages->removeElement($language);
    }

    /**
     * Get languages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLanguages()
    {
        return $this->languages;
    }
    
    
    
    
    
    /**
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     * @Assert\File(maxSize="2M")
     */
    public $avatarFile;
    
    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setAvatarFile(UploadedFile $file = null)
    {
        $this->avatarFile = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getAvatarFile()
    {
        return $this->avatarFile;
    }
    
    /**
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     * @Assert\File(maxSize="2M")
     */
    public $documentFile;
    
    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setDocumentFile(UploadedFile $file = null)
    {
        $this->documentFile = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getDocumentFile()
    {
        return $this->documentFile;
    }
    
    
}