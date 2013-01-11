<?php

namespace Zizoo\MessageBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessageRepository extends EntityRepository
{
    
    public function getInboxMessagesQueryBuilder($profile){
        $qb = $this->createQueryBuilder('m')
                                        ->leftJoin('m.recipients', 'r')
                                        ->leftJoin('m.sender_profile', 'sp')
                                        ->leftJoin('sp.user', 'su')
                                        ->select('DISTINCT m.id, m.subject, CONCAT(sp.lastName, CONCAT(\', \', CONCAT(sp.firstName, CONCAT(\' &lt;\', CONCAT(su.email, \'&gt;\'))))) as sender, m.sent')
                                        ->where('r.recipient_profile = :recipient')
                                        ->setParameter('recipient', $profile->getID());
        $qb->getQuery()->getArrayResult();
        return $qb;
    }
    
    public function getSentQueryBuilder($profile){
        $qb = $this->createQueryBuilder('m')
                                        ->leftJoin('m.recipients', 'r')
                                        ->leftJoin('r.recipient_profile', 'rp')
                                        ->leftJoin('m.sender_profile', 'sp')
                                        ->leftJoin('rp.user', 'ru')
                                        ->select('m.id, m.subject, CONCAT(rp.lastName, CONCAT(\', \', CONCAT(rp.firstName, CONCAT(\' &lt;\', CONCAT(ru.email, \'&gt;\'))))) as receiver, m.sent')
                                        ->where('sp.id = :sender')
                                        ->setParameter('sender', $profile->getID())
                                        ->orderBy('m.id, m.sent', 'desc');
        return $qb;
    }
    
}
