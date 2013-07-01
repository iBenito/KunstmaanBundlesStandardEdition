<?php

namespace Zizoo\BillingBundle\Entity;

use Zizoo\CharterBundle\Entity\Charter;

use Doctrine\ORM\EntityRepository;

/**
 * PayoutRepository
 *
 */
class PayoutRepository extends EntityRepository
{
    
    public function getSettledPayouts(Charter $charter=null)
    {
        $qb = $this->createQueryBuilder('payout')
                   ->leftJoin('payout.booking', 'booking')
                   ->leftJoin('booking.reservation', 'reservation')
                   ->leftJoin('reservation.boat', 'boat')
                   ->leftJoin('boat.charter', 'charter')
                   ->select('payout')
                   ->where('payout.settled = TRUE');
        
        if ($charter){
            $qb->andWhere('charter = :charter')
               ->setParameter('charter', $charter);
        }
        
        return $qb->getQuery()
                  ->getResult();
    }

}
