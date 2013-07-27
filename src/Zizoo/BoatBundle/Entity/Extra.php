<?php

namespace Zizoo\BoatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="Zizoo\BoatBundle\Entity\ExtraRepository")
 * @ORM\Table(name="boat_asset_extra")
 */
class Extra extends BoatAssetBase
{
    /**
     * @ORM\ManyToMany(targetEntity="Zizoo\BoatBundle\Entity\Boat", mappedBy="extra")
     */
    private $boats;
}