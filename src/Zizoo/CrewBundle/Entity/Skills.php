<?php

namespace Zizoo\CrewBundle\Entity;
use Zizoo\BaseBundle\Entity\BaseEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="skills")
 */
class Skills extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\UserBundle\Entity\User", inversedBy="skills")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /**
     * @ORM\OneToOne(targetEntity="Zizoo\CrewBundle\Entity\SkillLicense", mappedBy="skill", cascade={"persist","remove"})
     */
    protected $license;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $experience;

    /**
     * @ORM\ManyToOne(targetEntity="Zizoo\CrewBundle\Entity\SkillType")
     * @ORM\JoinColumn(name="skill_type", referencedColumnName="skill")
     */
    protected $skillType;

    /**
     * @ORM\ManyToMany(targetEntity="\Zizoo\BoatBundle\Entity\BoatType")
     * @ORM\JoinTable(name="skill_boat_types",
     *      joinColumns={@ORM\JoinColumn(name="skill_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="boat_type", referencedColumnName="id")}
     *      )
     **/
    protected $boatTypes;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->boats = new ArrayCollection();
    }

    /**
     * Set license
     *
     * @param string $license
     * @return Skills
     */
    public function setLicense($license)
    {
        $this->license = $license;
    
        return $this;
    }

    /**
     * Get license
     *
     * @return string 
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * Set experience
     *
     * @param integer $experience
     * @return Skills
     */
    public function setExperience($experience)
    {
        $this->experience = $experience;
    
        return $this;
    }

    /**
     * Get experience
     *
     * @return integer 
     */
    public function getExperience()
    {
        return $this->experience;
    }

    /**
     * Set user
     *
     * @param \Zizoo\UserBundle\Entity\User $user
     * @return Skills
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
     * Set skillType
     *
     * @param \Zizoo\CrewBundle\Entity\SkillType $skillType
     * @return Skills
     */
    public function setSkillType(\Zizoo\CrewBundle\Entity\SkillType $skillType = null)
    {
        $this->skillType = $skillType;

        return $this;
    }

    /**
     * Get skillType
     *
     * @return \Zizoo\CrewBundle\Entity\SkillType 
     */
    public function getSkillType()
    {
        return $this->skillType;
    }

    /**
     * Add boat type
     *
     * @param \Zizoo\BoatBundle\Entity\BoatType $boatType
     * @return Skill
     */
    public function addBoatTypes(\Zizoo\BoatBundle\Entity\BoatType $boatType)
    {
        $this->boatTypes->add($boatType);

        return $this;
    }

    /**
     * Remove Boat Type
     *
     * @param \Zizoo\BoatBundle\Entity\BoatType $boatType
     */
    public function removeBoatTypes(\Zizoo\BoatBundle\Entity\BoatType $boatType)
    {
        $this->boatTypes->removeElement($boatType);
    }

    public function getBoatTypes()
    {
        return $this->boatTypes;
    }

    public function setBoatTypes($boatTypes)
    {
        $this->boatTypes = $boatTypes;
        return $this;
    }

    /**
     * Get boat types
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLanguages()
    {
        return $this->boatTypes;
    }

}
