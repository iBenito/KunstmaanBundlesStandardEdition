<?php

namespace Zizoo\BoatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Extras
 *
 * @ORM\Entity
 * @ORM\Table(name="boat_assets", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"name"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"amenities" = "Amenities", "equipment" = "Equipment", "extra" = "Extra"})
 */
class BoatAssetBase
{

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=255)
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="display_order", type="integer")
     */
    private $order;


    public function __construct($id=null, $name=null, $order=null) {
        $this->id       = $id;
        $this->name     = $name;
        $this->order    = $order;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set name
     *
     * @param string $name
     * @return BoatType
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
     * Set order
     *
     * @param integer $order
     * @return BoatType
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

   
    
}