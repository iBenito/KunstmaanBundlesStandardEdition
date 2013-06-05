<?php

namespace Zizoo\CharterBundle\Entity;

use Zizoo\BaseBundle\Entity\BaseEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Charter
 *
 * @ORM\Table(name="charter")
 * @ORM\Entity(repositoryClass="Zizoo\CharterBundle\Entity\CharterRepository")
 */
class Charter extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToMany(targetEntity="\Zizoo\BoatBundle\Entity\Boat", inversedBy="charter")
     * @ORM\JoinTable(name="charter_boats",
     *      joinColumns={@ORM\JoinColumn(name="charter_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="boat_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    protected $boats;
    
    /**
     * @ORM\ManyToMany(targetEntity="\Zizoo\UserBundle\Entity\User", inversedBy="charter")
     * @ORM\JoinTable(name="charter_users",
     *      joinColumns={@ORM\JoinColumn(name="charter_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    protected $users;
    
    /**
     * @ORM\OneToOne(targetEntity="\Zizoo\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="admin_user_id", referencedColumnName="id", nullable=false)
     **/
    protected $adminUser;
    
    /**
     * @ORM\OneToOne(targetEntity="\Zizoo\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="billing_user_id", referencedColumnName="id", nullable=false)
     **/
    protected $billingUser;
        
    /**
     * @ORM\Column(name="charter_name", type="string", length=255)
     */
    protected $charterName;
    
    /**
     * @ORM\Column(name="charter_number", type="string", length=255, nullable=true)
     */
    protected $charterNumber;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $about;
    
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
    protected $file;
    
    /**
     * @ORM\OneToOne(targetEntity="Zizoo\AddressBundle\Entity\CharterAddress", mappedBy="charter")
     */
    protected $address;
    
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
     * Constructor
     */
    public function __construct()
    {
        $this->boats = new \Doctrine\Common\Collections\ArrayCollection(array());
        $this->users = new \Doctrine\Common\Collections\ArrayCollection(array());
        $this->reservations = new \Doctrine\Common\Collections\ArrayCollection(array());
        $this->bookings = new \Doctrine\Common\Collections\ArrayCollection(array());
        $d = new \DateTime();
        $this->setCreated($d);
        $this->setUpdated($d);
    }
    
    /**
     * Add boats
     *
     * @param \Zizoo\BoatBundle\Entity\Boat $boats
     * @return Charter
     */
    public function addBoat(\Zizoo\BoatBundle\Entity\Boat $boats)
    {
        $this->boats[] = $boats;
    
        return $this;
    }

    /**
     * Remove boats
     *
     * @param \Zizoo\BoatBundle\Entity\Boat $boats
     */
    public function removeBoat(\Zizoo\BoatBundle\Entity\Boat $boats)
    {
        $this->boats->removeElement($boats);
    }

    /**
     * Get boats
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBoats()
    {
        return $this->boats;
    }

    /**
     * Add users
     *
     * @param \Zizoo\UserBundle\Entity\User $users
     * @return Charter
     */
    public function addUser(\Zizoo\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;
    
        return $this;
    }

    /**
     * Remove users
     *
     * @param \Zizoo\UserBundle\Entity\User $users
     */
    public function removeUser(\Zizoo\UserBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set adminUser
     *
     * @param \Zizoo\UserBundle\Entity\User $adminUser
     * @return Charter
     */
    public function setAdminUser(\Zizoo\UserBundle\Entity\User $adminUser = null)
    {
        $this->adminUser = $adminUser;
    
        return $this;
    }

    /**
     * Get adminUser
     *
     * @return \Zizoo\UserBundle\Entity\User 
     */
    public function getAdminUser()
    {
        return $this->adminUser;
    }

    /**
     * Set billingUser
     *
     * @param \Zizoo\UserBundle\Entity\User $billingUser
     * @return Charter
     */
    public function setBillingUser(\Zizoo\UserBundle\Entity\User $billingUser = null)
    {
        $this->billingUser = $billingUser;
    
        return $this;
    }

    /**
     * Get billingUser
     *
     * @return \Zizoo\UserBundle\Entity\User 
     */
    public function getBillingUser()
    {
        return $this->billingUser;
    }
    
    /**
     * Set about
     *
     * @param string $about
     * @return Charter
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
     * @return Charter
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
     * @return Charter
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
     * Set charter name
     *
     * @param string $charterName
     * @return Charter
     */
    public function setCharterName($charterName)
    {
        $this->charterName = $charterName;
    
        return $this;
    }

    /**
     * Get charter name
     *
     * @return string 
     */
    public function getCharterName()
    {
        return $this->charterName;
    }

    
    /**
     * Set charter number
     *
     * @param string $charterNumber
     * @return Charter
     */
    public function setCharterNumber($charterNumber)
    {
        $this->charterNumber = $charterNumber;
    
        return $this;
    }

    /**
     * Get charter number
     *
     * @return string 
     */
    public function getCharterNumber()
    {
        return $this->charterNumber;
    }
    
    /**
     * Set address
     *
     * @param \Zizoo\AddressBundle\Entity\CharterAddress $address
     * @return Charter
     */
    public function setAddress(\Zizoo\AddressBundle\Entity\CharterAddress $address = null)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return \Zizoo\AddressBundle\Entity\CharterAddress 
     */
    public function getAddress()
    {
        return $this->address;
    }
}