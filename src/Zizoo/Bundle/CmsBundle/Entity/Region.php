<?php

namespace Zizoo\Bundle\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="zizoo_cmsbundle_region")
 * @ORM\Entity(repositoryClass="Zizoo\Bundle\CmsBundle\Entity\RegionRepository")
 */
class Region extends AbstractEntity
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer")
     */
    private $code;

    /**
     * Set code.
     *
     * @param $code
     *
     * @return this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Region")
     */
    private $parent;

    /**
     * Set parent.
     *
     * @param string $parent
     *
     * @return this
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent.
     *
     * @return Region
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lat;

    /**
     * Set lat.
     *
     * @param string $lat
     *
     * @return this
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat.
     *
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lng;

    /**
     * Set lng.
     *
     * @param string $lng
     *
     * @return this
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng.
     *
     * @return string
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @ORM\Column(name="iso", type="string", length=2, nullable=true)
     */
    protected $iso;

    /**
     * @param $iso
     *
     * @return $this
     */
    public function setIso($iso)
    {
        $this->iso = $iso;

        return $this;
    }

    /**
     * Get iso.
     *
     * @return string
     */
    public function getIso()
    {
        return $this->iso;
    }

    /**
     * @ORM\Column(name="iso3", type="string", length=6, nullable=true)
     */
    protected $iso3;

    /**
     * Set iso3.
     *
     * @param string $iso3
     *
     * @return Country
     */
    public function setIso3($iso3)
    {
        $this->iso3 = $iso3;

        return $this;
    }

    /**
     * Get iso3.
     *
     * @return string
     */
    public function getIso3()
    {
        return $this->iso3;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="internal_name", type="string", length=255, nullable=true)
     */
    protected $internalName;

    /**
     * @param string $internalName
     */
    public function setInternalName($internalName)
    {
        $this->internalName = $internalName;
    }

    /**
     * @return string
     */
    public function getInternalName()
    {
        return $this->internalName;
    }

    /**
     * Magic function for proxying requests to translatable fields.
     *
     * @param $method
     * @param $arguments
     */
    public function __call($method, $arguments)
    {
        return \Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessor()->getValue($this->translate(), $method);
    }
}
