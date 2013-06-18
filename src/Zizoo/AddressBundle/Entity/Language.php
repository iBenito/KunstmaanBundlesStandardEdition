<?php

namespace Zizoo\AddressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Language
 * @ORM\Table(name="language")
 * @ORM\Entity(repositoryClass="Zizoo\AddressBundle\Entity\LanguageRepository")
 */
class Language
{
    /**
     * @var string
     *
     * @ORM\Column(name="language_code", type="string", length=3)
     * @ORM\Id
     */
    private $language_code;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=80)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=80)
     */
    private $native_name;

    /**
     * @ORM\ManyToMany(targetEntity="\Zizoo\ProfileBundle\Entity\Profile", mappedBy="languages")
     */
    protected $profile;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->profile = new ArrayCollection();
    }

    /**
     * Set language_code
     *
     * @param string $languageCode
     * @return Language
     */
    public function setLanguageCode($languageCode)
    {
        $this->language_code = $languageCode;

        return $this;
    }

    /**
     * Get language_code
     *
     * @return string 
     */
    public function getLanguageCode()
    {
        return $this->language_code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Language
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
     * Set native_name
     *
     * @param string $nativeName
     * @return Language
     */
    public function setNativeName($nativeName)
    {
        $this->native_name = $nativeName;

        return $this;
    }

    /**
     * Get native_name
     *
     * @return string 
     */
    public function getNativeName()
    {
        return $this->native_name;
    }

    /**
     * Set profile
     *
     * @param \Zizoo\ProfileBundle\Entity\Profile $profile
     * @return \Zizoo\AddressBundle\Entity\Language
     */
    public function setProfile(\Zizoo\ProfileBundle\Entity\Profile $profile)
    {
        $this->profile = new ArrayCollection(array($profile));
        return $this;
    }

    /**
     * Get profile
     *
     * @return \Zizoo\ProfileBundle\Entity\Profile $profile
     */
    public function getProfile() {
        return $this->profile->first();
    }

}
