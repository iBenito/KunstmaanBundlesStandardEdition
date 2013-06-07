<?php

namespace Zizoo\BoatBundle\Entity;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Entity\OptionalExtra;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * OptionalExtraRepository
 *
 */
class OptionalExtraRepository extends EntityRepository
{
    public function getOptionalExtrasForBoat(Boat $boat)
    {
        $qb = $this->createQueryBuilder('optional_extra')
                   ->leftJoin('optional_extra.boats', 'boats')
                   ->select('optional_extra, boats')
                   ->where('boats = :boat OR boats IS NULL')
                   ->setParameter('boat', $boat);
                
//        $query = $this->getEntityManager()
//        ->createQuery('
//            SELECT optional_extra
//            FROM ZizooBoatBundle:OptionalExtra optional_extra
//            LEFT OUTER JOIN optional_extra
//            ON be.optionalextra_id = e.id
//            WHERE (boat = : boat)')
//                ->setParameter('boat', $boat);
 
        //return $query->getResult();
        return $qb->getQuery()
                  ->getResult();
    }
    
}
