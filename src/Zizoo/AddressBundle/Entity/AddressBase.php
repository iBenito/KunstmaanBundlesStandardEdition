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
 * @ORM\DiscriminatorMap({"profile" = "ProfileAddress", "boat" = "BoatAddress", "reservation" = "ReservationAddress", "charter" = "CharterAddress"})
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
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="addr_line_1", type="string", length=255, nullable=true)
     */
    protected $addressLine1;

    /**
     * @var string
     *
     * @ORM\Column(name="addr_line_2", type="string", length=255, nullable=true)
     */
    protected $addressLine2;

    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="string", length=255, nullable=true)
     */
    protected $postcode;

    /**
     * @var string
     *
     * @ORM\Column(name="locality", type="string", length=255, nullable=true)
     */
    protected $locality;

    /**
     * @var string
     *
     * @ORM\Column(name="sub_locality", type="string", length=255, nullable=true)
     */
    protected $subLocality;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255, nullable=true)
     */
    protected $state;

    /**
     * @var string
     *
     * @ORM\Column(name="province", type="string", length=255, nullable=true)
     */
    protected $province;

    
    /**
     * @var string
     *
     * @ORM\Column(name="extra1", type="string", length=255, nullable=true)
     */
    protected $extra1;

    /**
     * @var string
     *
     * @ORM\Column(name="extra2", type="string", length=255, nullable=true)
     */
    protected $extra2;

    /**
     * @ORM\OneToOne(targetEntity="Country")
     * @ORM\JoinColumn(name="country", referencedColumnName="iso")
     **/
    protected $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lat;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lng;
    

    public function __construct(AddressBase $address=null) {
        if ($address){
            $this->setCountry($address->getCountry());
            $this->setExtra1($address->getExtra1());
            $this->setExtra2($address->getExtra2());
            $this->setLocality($address->getLocality());
            $this->setPostcode($address->getPostcode());
            $this->setProvince($address->getProvince());
            $this->setState($address->getState());
            $this->setAddressLine1($address->getAddressLine1());
            $this->setAddressLine2($address->getAddressLine2());
            $this->setSubLocality($address->getSubLocality());
            $this->setLat($address->getLat());
            $this->setLng($address->getLng());
        }
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
     * Set address line 1
     *
     * @param string $addressLine1
     * @return AddressBase
     */
    public function setAddressLine1($addressLine1)
    {
        $this->addressLine1 = $addressLine1;
    
        return $this;
    }

    /**
     * Get address line 1
     *
     * @return string 
     */
    public function getAddressLine1()
    {
        return $this->addressLine1;
    }

    /**
     * Set address line 2
     *
     * @param string $addressLine2
     * @return AddressBase
     */
    public function setAddressLine2($addressLine2)
    {
        $this->addressLine2 = $addressLine2;
    
        return $this;
    }

    /**
     * Get address line 2
     *
     * @return string 
     */
    public function getAddressLine2()
    {
        return $this->addressLine2;
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
        $this->subLocality = $subLocality;
    
        return $this;
    }

    /**
     * Get sub_locality
     *
     * @return string 
     */
    public function getSubLocality()
    {
        return $this->subLocality;
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
    
    /**
     * Set lat
     *
     * @param string $lat
     * @return BoatAddress
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    
        return $this;
    }

    /**
     * Get lat
     *
     * @return string 
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param string $lng
     * @return BoatAddress
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    
        return $this;
    }

    /**
     * Get lng
     *
     * @return string 
     */
    public function getLng()
    {
        return $this->lng;
    }
    
    
    
}