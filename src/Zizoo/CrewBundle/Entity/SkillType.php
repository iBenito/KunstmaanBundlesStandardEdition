<?php

namespace Zizoo\CrewBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SkillType
 *
 * @ORM\Table(name="skill_type")
 * @ORM\Entity
 */
class SkillType
{

    /**
     * @var string
     *
     * @ORM\Column(name="skill", type="string", length=255)
     * @ORM\Id
     */
    protected $skill;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;


    /**
     * Set skill
     *
     * @param string $skill
     * @return SkillType
     */
    public function setSkill($skill)
    {
        $this->skill = $skill;

        return $this;
    }

    /**
     * Get skill
     *
     * @return string 
     */
    public function getSkill()
    {
        return $this->skill;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return SkillType
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
}
