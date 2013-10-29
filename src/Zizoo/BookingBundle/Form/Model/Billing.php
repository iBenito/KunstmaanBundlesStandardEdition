<?php
// src/Zizoo/BookingBundle/Form/Model/Billing.php
namespace Zizoo\BookingBundle\Form\Model;

use Zizoo\ProfileBundle\Entity\Profile;

class Billing
{
    
    protected $firstName;
    protected $lastName;
    protected $addressLine1;
    protected $addressLine2;
    protected $locality;
    protected $subLocality;
    protected $postcode;
    protected $country;
    protected $lat;
    protected $lng;
    
    public function __construct(Profile $profile=null) {
        if ($profile!==null){
            $this->firstName        = $profile->getFirstName();
            $this->lastName         = $profile->getLastName();
            
            $profileAddress         = $profile->getAddress();
            if ($profileAddress!==null){
                $this->addressLine1     = $profileAddress->getAddressLine1();
                $this->addressLine2     = $profileAddress->getAddressLine2();
                $this->locality         = $profileAddress->getLocality();
                $this->subLocality      = $profileAddress->getSubLocality();
                $this->postcode         = $profileAddress->getPostcode();
                $this->country          = $profileAddress->getCountry();
            }
            
        }
    }

    public function getFirstName()
    {
        return $this->firstName;
    }
    
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }
    
    public function getLastName()
    {
        return $this->lastName;
    }
    
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }
    
    public function getAddressLine1()
    {
        return $this->addressLine1;
    }
    
    public function setAddressLine1($addressLine1)
    {
        $this->addressLine1 = $addressLine1;
    }
    
    public function getAddressLine2()
    {
        return $this->addressLine2;
    }
    
    public function setAddressLine2($addressLine2)
    {
        $this->addressLine2 = $addressLine2;
    }
    
    public function getLocality()
    {
        return $this->locality;
    }
    
    public function setLocality($locality)
    {
        $this->locality = $locality;
    }
    
    public function getSubLocality()
    {
        return $this->subLocality;
    }
    
    public function setSubLocality($subLocality)
    {
        $this->subLocality = $subLocality;
    }
    
    public function getPostcode()
    {
        return $this->postcode;
    }
    
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }
    
    public function getCountry()
    {
        return $this->country;
    }
    
    public function setCountry($country)
    {
        $this->country = $country;
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
?>