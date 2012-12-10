<?php

namespace Zizoo\AddressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country
 * @ORM\Table(name="country")
 * @ORM\Entity(repositoryClass="Zizoo\AddressBundle\Entity\CountryRepository")
 */
class Country
{
    /**
     * @var string
     *
     * @ORM\Column(name="iso", type="string", length=2, nullable=false)
     * @ORM\Id
     */
    private $iso;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="printable_name", type="string", length=80, nullable=false)
     */
    private $printableName;

    /**
     * @var string
     *
     * @ORM\Column(name="iso3", type="string", length=3, nullable=true)
     */
    private $iso3;

    /**
     * @var integer
     *
     * @ORM\Column(name="numcode", type="smallint", nullable=true)
     */
    private $numcode;


    public function setIso($iso){
        $this->iso = $iso;
        
        return $this;
    }
    
    /**
     * Get iso
     *
     * @return string 
     */
    public function getIso()
    {
        return $this->iso;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Country
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set printableName
     *
     * @param string $printableName
     * @return Country
     */
    public function setPrintableName($printableName)
    {
        $this->printableName = $printableName;
    
        return $this;
    }

    /**
     * Get printableName
     *
     * @return string 
     */
    public function getPrintableName()
    {
        return $this->printableName;
    }

    /**
     * Set iso3
     *
     * @param string $iso3
     * @return Country
     */
    public function setIso3($iso3)
    {
        $this->iso3 = $iso3;
    
        return $this;
    }

    /**
     * Get iso3
     *
     * @return string 
     */
    public function getIso3()
    {
        return $this->iso3;
    }

    /**
     * Set numcode
     *
     * @param integer $numcode
     * @return Country
     */
    public function setNumcode($numcode)
    {
        $this->numcode = $numcode;
    
        return $this;
    }

    /**
     * Get numcode
     *
     * @return integer 
     */
    public function getNumcode()
    {
        return $this->numcode;
    }
}