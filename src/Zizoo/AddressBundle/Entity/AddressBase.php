<?php

namespace Zizoo\AddressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AddressBase
 *
 * @ORM\Entity
 * @ORM\Table(name="address")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"profile" = "ProfileAddress", "boat" = "BoatAddress"})
 */
class AddressBase
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
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=255, nullable=true)
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(name="premise", type="string", length=255, nullable=true)
     */
    private $premise;

    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="string", length=255, nullable=true)
     */
    private $postcode;

    /**
     * @var string
     *
     * @ORM\Column(name="locality", type="string", length=255, nullable=true)
     */
    private $locality;

    /**
     * @var string
     *
     * @ORM\Column(name="sub_locality", type="string", length=255, nullable=true)
     */
    private $sub_locality;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="province", type="string", length=255, nullable=true)
     */
    private $province;

    /**
     * @var string
     *
     * @ORM\Column(name="extra1", type="string", length=255, nullable=true)
     */
    private $extra1;

    /**
     * @var string
     *
     * @ORM\Column(name="extra2", type="string", length=255, nullable=true)
     */
    private $extra2;

    /**
     * @ORM\OneToOne(targetEntity="Country")
     * @ORM\JoinColumn(name="country", referencedColumnName="iso")
     **/
    private $country;


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
     * Set street
     *
     * @param string $street
     * @return AddressBase
     */
    public function setStreet($street)
    {
        $this->street = $street;
    
        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set premise
     *
     * @param string $premise
     * @return AddressBase
     */
    public function setPremise($premise)
    {
        $this->premise = $premise;
    
        return $this;
    }

    /**
     * Get premise
     *
     * @return string 
     */
    public function getPremise()
    {
        return $this->premise;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     * @return AddressBase
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    
        return $this;
    }

    /**
     * Get postcode
     *
     * @return string 
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set locality
     *
     * @param string $locality
     * @return AddressBase
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
    
        return $this;
    }

    /**
     * Get locality
     *
     * @return string 
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Set sub_locality
     *
     * @param string $subLocality
     * @return AddressBase
     */
    public function setSubLocality($subLocality)
    {
        $this->sub_locality = $subLocality;
    
        return $this;
    }

    /**
     * Get sub_locality
     *
     * @return string 
     */
    public function getSubLocality()
    {
        return $this->sub_locality;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return AddressBase
     */
    public function setState($state)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set province
     *
     * @param string $province
     * @return AddressBase
     */
    public function setProvince($province)
    {
        $this->province = $province;
    
        return $this;
    }

    /**
     * Get province
     *
     * @return string 
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set extra1
     *
     * @param string $extra1
     * @return AddressBase
     */
    public function setExtra1($extra1)
    {
        $this->extra1 = $extra1;
    
        return $this;
    }

    /**
     * Get extra1
     *
     * @return string 
     */
    public function getExtra1()
    {
        return $this->extra1;
    }

    /**
     * Set extra2
     *
     * @param string $extra2
     * @return AddressBase
     */
    public function setExtra2($extra2)
    {
        $this->extra2 = $extra2;
    
        return $this;
    }

    /**
     * Get extra2
     *
     * @return string 
     */
    public function getExtra2()
    {
        return $this->extra2;
    }



    /**
     * Set country
     *
     * @param \Zizoo\AddressBundle\Entity\Country $country
     * @return AddressBase
     */
    public function setCountry(\Zizoo\AddressBundle\Entity\Country $country = null)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return \Zizoo\AddressBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }
}