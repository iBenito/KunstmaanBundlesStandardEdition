<?php

namespace Zizoo\BookingBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * BookingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PaymentRepository extends EntityRepository
{
    
    public function getPayments($charter=null, $providerStatuses = null)
    {
        $qb = $this->createQueryBuilder('payment')
                   ->leftJoin('payment.booking', 'booking')
                   ->leftJoin('booking.reservation', 'reservation')
                   ->leftJoin('reservation.boat', 'boat')
                   ->leftJoin('reservation.guest', 'guest')
                   ->leftJoin('boat.charter', 'charter')
                   ->select('payment');
        
        $firstWhere = true;
        if ($charter!==null){
            $qb->where('charter = :charter')
               ->setParameter('charter', $charter);
            $firstWhere = false;
        }

        if ($providerStatuses){
            if (!is_array($providerStatuses)){
                throw new \Exception('status must be null or an array of arrays');
            }
            $dqlArr = array();
            foreach ($providerStatuses as $provider => $statuses){
                $dqlStr = ' (payment.provider = ' . $provider . ' ';
                $innerDqlArr = array();
                foreach ($statuses as $status){
                    $innerDqlArr[] = 'payment.providerStatus = ' . $status;
                }
                if (count($innerDqlArr)>0){
                    $dqlStr .= 'AND (' . implode(' OR ', $innerDqlArr) . ') ';
                }
                $dqlStr .= ') ';
                $dqlArr[] = $dqlStr;
            }
            if (count($dqlArr)>0){
                $dqlStr = '('. implode(' OR ', $dqlArr) .')';
                if ($firstWhere){
                    $qb->where($dqlStr);
                    $firstWhere = false;
                } else {
                    $qb->andWhere($dqlStr);
                }
            }
        }

        return $qb->getQuery()
                  ->getResult();
    }

    public function getOutstandingPayments($charter)
    {
        return $this->getPayments($charter);
    }

    public function getSettledPayments($charter)
    {
        $providerStatuses = array(
            Payment::PROVIDER_BRAINTREE         => array(Payment::BRAINTREE_STATUS_SETTLED),
            Payment::PROVIDER_BANK_TRANSFER     => array(Payment::BANK_TRANSFER_SETTLED)
        );
        return $this->getPayments($charter, $providerStatuses);
    }
}
