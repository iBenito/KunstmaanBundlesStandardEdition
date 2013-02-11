<?php
// src/Zizoo/BookingBundle/Form/Model/CreditCard.php
namespace Zizoo\BookingBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;


class BillingAddress
{
    
    protected $firstName;
    protected $lastName;
    protected $streetAddress;
    protected $extendedAddress;
    protected $locality;
    protected $region;
    protected $postalCode;
    protected $country;
    


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
    
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }
    
    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = $streetAddress;
    }
    
    public function getExtendedAddress()
    {
        return $this->extendedAddress;
    }
    
    public function setExtendedAddress($extendedAddress)
    {
        $this->extendedAddress = $extendedAddress;
    }
    
    public function getLocality()
    {
        return $this->locality;
    }
    
    public function setLocality($locality)
    {
        $this->locality = $locality;
    }
    
    public function getRegion()
    {
        return $this->region;
    }
    
    public function setRegion($region)
    {
        $this->region = $region;
    }
    
    public function getPostalCode()
    {
        return $this->postalCode;
    }
    
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }
    
    public function getCountryCodeAlpha2()
    {
        return $this->country;
    }
    
    public function setCountryCodeAlpha2($country)
    {
        $this->country = $country;
    }
    
   
}
?>