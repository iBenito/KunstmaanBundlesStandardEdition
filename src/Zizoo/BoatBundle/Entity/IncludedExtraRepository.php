<?php

namespace Zizoo\BoatBundle\Entity;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Entity\OptionalExtra;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * IncludedExtraRepository
 *
 */
class IncludedExtraRepository extends EntityRepository
{
    public function getIncludedExtrasForBoat(Boat $boat)
    {
        $qb = $this->createQueryBuilder('included_extra')
                   ->leftJoin('included_extra.boats', 'boats')
                   ->select('included_extra, boats')
                   ->where('boats = :boat OR boats IS NULL')
                   ->setParameter('boat', $boat);
                
        return $qb->getQuery()
                  ->getResult();
    }
    
}
