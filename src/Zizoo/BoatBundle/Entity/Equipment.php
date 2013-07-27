<?php

namespace Zizoo\BoatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoatType
 *
 * @ORM\Table(name="boat_asset_equipment")
 * @ORM\Entity
 */
class Equipment extends BoatAssetBase
{
    /**
     * @ORM\ManyToMany(targetEntity="Zizoo\BoatBundle\Entity\Boat", mappedBy="equipment")
     */
    private $boats;
}