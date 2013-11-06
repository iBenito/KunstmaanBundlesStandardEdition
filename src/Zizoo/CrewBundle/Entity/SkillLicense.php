<?php
namespace Zizoo\CrewBundle\Entity;

use Zizoo\CrewBundle\Entity\Skills;
use Zizoo\MediaBundle\Entity\Media;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class SkillLicense extends Media {
    
    /**
     * @ORM\OneToOne(targetEntity="\Zizoo\CrewBundle\Entity\Skills", inversedBy="license")
     * @ORM\JoinColumn(name="skill_id", referencedColumnName="id")
     */
    protected $skill;

    /**
     * Set license
     *
     * @param \Zizoo\CrewBundle\Entity\Skills $skill
     * @return SkillLicense
     */
    public function setSkill(Skills $skill = null)
    {
        $this->skill = $skill;
    
        return $this;
    }

    /**
     * Get license
     *
     * @return \Zizoo\CrewBundle\Entity\Skills
     */
    public function getSkill()
    {
        return $this->skill;
    }

    public function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'documents/licenses/'.$this->skill->getId();
    }


//    /**
//     * @ORM\PrePersist()
//     * @ORM\PreUpdate()
//     */
//    public function preUpload()
//    {
//        if (null !== $this->getFile()) {
//            // do whatever you want to generate a unique name
//            $this->setPath($this->getFile()->guessExtension());
//        }
//    }
//
//    /**
//     * @ORM\PostPersist()
//     * @ORM\PostUpdate()
//     */
//    public function upload()
//    {
//        // the file property can be empty if the field is not required
//        if (null === $this->getFile()) {
//            return;
//        }
//
//        // move takes the target directory and then the
//        // target filename to move to
//        $this->getFile()->move(
//            $this->getUploadRootDir(),
//            $this->getPath()
//        );
//
//        // check if we have an old image
//        if (isset($this->temp)) {
//            // delete the old image
//            unlink($this->getUploadRootDir().'/'.$this->temp);
//            // clear the temp image path
//            $this->temp = null;
//        }
//
//        // clean up the file property as you won't need it anymore
//        $this->file = null;
//    }

}