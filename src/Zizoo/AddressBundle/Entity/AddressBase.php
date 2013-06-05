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
     * @ORM\Column(name="street", type="string", length=255, nullable=true)
     */
    protected $street;

    /**
     * @var string
     *
     * @ORM\Column(name="premise", type="string", length=255, nullable=true)
     */
    protected $premise;

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
            $this->setPremise($address->getPremise());
            $this->setProvince($address->getProvince());
            $this->setState($address->getState());
            $this->setStreet($address->getStreet());
            $this->setSubLocality($address->getSubLocality());
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
    
    /**
     * Gets a formatted boat address, used by fetchGeo() to search.
     * @return string   Formatted address
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function getFormattedAddress(){
        $address = array();
        $street     = $this->getStreet();
        $premise    = $this->getPremise();
        if ($street && $street!=''){
            if ($premise && $premise!=''){
                $address[] = $street . ' ' . $premise;
            } else {
                $address[] = $street;
            }
        }
        
        $postcode = $this->getPostcode();
        if ($postcode && $postcode!=''){
            $address[] = $postcode;
        }
        
        $locality = $this->getLocality();
        if ($locality && $locality!=''){
            $address[] = $locality;
        }
        
        $country = $this->getCountry()->getPrintableName();
        if ($country && $country!=''){
            $address[] = $country;
        }
        
        return implode(',', $address);
    }
    
    /**
     * Fetch geo data (lat, lng) for boat address from Google Maps service. Requires CURL.
     * TODO: Fail gracefully.
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function fetchGeo(){
        // jSON URL which should be requested
        $json_url = 'http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($this->getFormattedAddress()).'&sensor=false';

        // Initializing curl
        $ch = curl_init( $json_url );

        // Configuring curl options
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Content-type: application/json')
        );

        // Setting curl options
        curl_setopt_array( $ch, $options );

        // Getting results
        $result = json_decode(curl_exec($ch)); 

        if ($result->results && count($result->results)>0){
            $geoLocation = $result->results[0]->geometry->location;

            $this->setLat($geoLocation->lat);
            $this->setLng($geoLocation->lng);
        }
    }
    
}