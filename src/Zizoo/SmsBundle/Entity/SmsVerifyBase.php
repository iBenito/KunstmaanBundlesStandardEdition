<?php

namespace Zizoo\SmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VerifyBase
 *
 * @ORM\Entity
 * @ORM\Table(name="verify")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"profile" = "ProfileSmsVerify", "booking" = "BookingSmsVerify"})
 */
class SmsVerifyBase
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=60, unique=true, nullable=true)
     */
    protected $phone;

    /**
     * @ORM\Column(name="code", type="integer")
     */
    protected $code;

    /**
     * @ORM\Column(name="verified", type="boolean")
     */
    protected $verified;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->verified = 0;
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
     * Set phone
     *
     * @param string $phone
     * @return VerifyBase
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
     * Set code
     *
     * @param integer $code
     * @return VerifyBase
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set verified
     *
     * @param boolean $verified
     * @return VerifyBase
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * Get verified
     *
     * @return boolean 
     */
    public function getVerified()
    {
        return $this->verified;
    }
}
