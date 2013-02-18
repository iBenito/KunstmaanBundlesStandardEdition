<?php
namespace Zizoo\ProfileBundle\Entity;
use Zizoo\BaseBundle\Entity\BaseEntity;

use Zizoo\ProfileBundle\Entity\Profile\NotificationSettings;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @var File  - not a persisted field!
     */
    public $file;
   
    /**
     * @ORM\OneToOne(targetEntity="Zizoo\ProfileBundle\Entity\Profile\NotificationSettings", cascade={"persist"})
     */    
    protected $notification_settings;
        
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
     * File upload pre processing
     * 
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->picture = $filename.'.'.$this->file->guessExtension();
        }

    }
   
    /**
     * File upload
     * 
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function uploadPicture()
    {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }

        // move takes the target directory and then the
        // target filename to move to
        // compute a random name and try to guess the extension (more secure)
        $extension = $this->file->guessExtension();
        if (!$extension) {
            // extension cannot be guessed
            $extension = 'bin';
        }
        
        $this->file->move(
            $this->getUploadRootDir(),
            $this->picture
        );

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }
    
    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }
    
    public function getAbsolutePath()
    {
        return null === $this->picture
            ? null
            : $this->getUploadRootDir().'/'.$this->picture;
    }

    public function getWebPath()
    {
        return null === $this->picture
            ? null
            : $this->getUploadDir().'/'.$this->picture;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'images/profile/'.$this->id;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $notificationSettings = new NotificationSettings();
        $notificationSettings->setMessage(true);
        $notificationSettings->setEnquiry(true);
        $notificationSettings->setBooked(true);
        $notificationSettings->setBooking(true);
        $notificationSettings->setReview(true);
        $this->setNotificationSettings($notificationSettings);
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
}