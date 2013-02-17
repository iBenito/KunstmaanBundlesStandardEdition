<?php

namespace Zizoo\AddressBundle\Entity;
use Zizoo\BaseBundle\Entity\BaseEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Marina
 * @ORM\Table(name="marina")
 * @ORM\Entity(repositoryClass="Zizoo\AddressBundle\Entity\MarinaRepository")
 */
class Marina extends BaseEntity
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lat;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lng;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=80, nullable=false)
     */
    private $name;

    /**
     * Set lat
     *
     * @param string $lat
     * @return Marina
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
     * @return Marina
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
     * Set name
     *
     * @param string $name
     * @return Marina
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