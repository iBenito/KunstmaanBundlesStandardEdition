<?php

namespace Zizoo\ReservationBundle\Entity;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\CharterBundle\Entity\Charter;
use Zizoo\UserBundle\Entity\User;

use Doctrine\ORM\EntityRepository;

/**
 * ReservationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReservationRepository extends EntityRepository
{
        
    public function getReservationBoatIds($resFrom, $resTo) {
        
        $qb = $this->createQueryBuilder('r')
                    ->select('r')
                    ->where('r.checkIn BETWEEN :check_in AND :check_out')
                    ->orWhere('r.checkOut BETWEEN :check_in AND :check_out')
                    ->orWhere(':check_in BETWEEN r.checkIn AND r.checkOut')
                    ->setParameter('check_in', $resFrom)
                    ->setParameter('check_out', $resTo);
        
        return $qb->getQuery()->getResult();
        
    }
    
    public function getReservations(Charter $charter=null, User $user=null, Boat $boat=null, \DateTime $from=null, \DateTime $to=null, $statusArr=null, Reservation $exceptReservation=null)
    {
         $qb = $this->createQueryBuilder('reservation')
                    ->select('reservation, boat')
                    ->leftJoin('reservation.boat', 'boat')
                    ->leftJoin('boat.charter', 'charter');
         
         $firstWhere = true;
         if ($charter){
             $qb = $qb->where('charter = :charter')
                       ->setParameter('charter', $charter);
             $firstWhere = false;
         }
         
         if ($user){
             if ($firstWhere){
                 $qb = $qb->where('reservation.guest = :guest')
                       ->setParameter('guest', $user);
                 $firstWhere = false;
             } else {
                 $qb = $qb->andWhere('reservation.guest = :guest')
                       ->setParameter('guest', $user);
             }
         }
         
         if ($boat){
             if ($firstWhere){
                 $qb = $qb->where('boat = :boat')
                            ->setParameter('boat', $boat);
                 $firstWhere = false;
             } else {
                 $qb = $qb->andWhere('boat = :boat')
                            ->setParameter('boat', $boat);
             }
         }
         
         if ($from && $to){
             if ($firstWhere){
                 $qb = $qb->where('(reservation.checkIn BETWEEN :check_in AND :check_out) OR (reservation.checkOut BETWEEN :check_in AND :check_out) OR (:check_in BETWEEN reservation.checkIn AND reservation.checkOut)')
                            ->setParameter('check_in', $from)
                            ->setParameter('check_out', $to);
                 $firstWhere = false;
             } else {
                 $qb = $qb->andWhere('(reservation.checkIn BETWEEN :check_in AND :check_out) OR (reservation.checkOut BETWEEN :check_in AND :check_out) OR (:check_in BETWEEN reservation.checkIn AND reservation.checkOut)')
                            ->setParameter('check_in', $from)
                            ->setParameter('check_out', $to);
             }
         } else if ($from){
             if ($firstWhere){
                 $qb = $qb->where('reservation.checkIn < :check_in')
                            ->setParameter('check_in', $from);
                 $firstWhere = false;
             } else {
                 $qb = $qb->andWhere('reservation.checkIn < :check_in')
                            ->setParameter('check_in', $from);
             }
         } else if ($to){
             if ($firstWhere){
                 $qb = $qb->where('reservation.checkOut > :check_out')
                            ->setParameter('check_out', $to);
                 $firstWhere = false;
             } else {
                 $qb = $qb->andWhere('reservation.checkOut > :check_out')
                            ->setParameter('check_out', $to);
             }
         }
         
         if ($statusArr && count($statusArr)>0){
             if ($firstWhere){
                 $qb = $qb->where('reservation.status IN (:status)')
                            ->setParameter('status', $statusArr);
                 $firstWhere = false;
             } else {
                $qb = $qb->andWhere('reservation.status IN (:status)')
                            ->setParameter('status', $statusArr);
             }
         }
         
         if ($exceptReservation){
             if ($firstWhere){
                 $qb = $qb->where('reservation != (:except_reservation)')
                            ->setParameter('except_reservation', $exceptReservation);
                 $firstWhere = false;
             } else {
                 $qb = $qb->andWhere('reservation != (:except_reservation)')
                            ->setParameter('except_reservation', $exceptReservation);
             }
         }
         
         return $qb->getQuery()->getResult();
    }
    
    public function getReservationRequests(Charter $charter=null, Boat $boat=null) {
        return $this->getReservations($charter, null, $boat, null, null, array(Reservation::STATUS_REQUESTED));
    }

    public function getUpcomingWeekReservations(Charter $charter=null) {
        $from = new \DateTime();
        $from->add(new \DateInterval('P10D'));
        return $this->getReservations($charter, null, null, $from, null, array(Reservation::STATUS_ACCEPTED));
    }

    public function getAcceptedReservations(Charter $charter=null) {
        return $this->getReservations($charter, null, null, null, null, array(Reservation::STATUS_ACCEPTED));
    }
    
    public function findByIds($ids)
    {
        $qb = $this->createQueryBuilder('reservation')
                    ->select('reservation')
                    ->where('reservation.id IN (:ids)')
                    ->setParameter('ids', $ids);
        
        return $qb->getQuery()->getResult();
    }
        
    
    
}
