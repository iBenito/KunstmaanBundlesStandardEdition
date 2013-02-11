<?php

namespace Zizoo\BoatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoatType
 *
 * @ORM\Table(name="boat_type")
 * @ORM\Entity
 */
class BoatType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="order_num", type="integer")
     */
    private $orderNum;

    public function __construct($name=null, $orderNum=null) {
        $this->name     = $name;
        $this->orderNum = $orderNum;
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
     * Set order_num
     *
     * @param integer $orderNum
     * @return BoatType
     */
    public function setOrderNum($orderNum)
    {
        $this->orderNum = $orderNum;
    
        return $this;
    }

    /**
     * Get order_num
     *
     * @return integer 
     */
    public function getOrderNum()
    {
        return $this->orderNum;
    }
}
