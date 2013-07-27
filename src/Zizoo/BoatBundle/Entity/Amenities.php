<?php

namespace Zizoo\BoatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Boat Amenities
 *
 * @ORM\Table(name="boat_asset_amenities")
 * @ORM\Entity
 */
class Amenities extends BoatAssetBase
{
    /**
     * @ORM\ManyToMany(targetEntity="Zizoo\BoatBundle\Entity\Boat", mappedBy="amenities")
     */
    private $boats;
    
}