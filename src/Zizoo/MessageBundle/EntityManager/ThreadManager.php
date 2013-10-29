<?php

namespace Zizoo\MessageBundle\EntityManager;

use FOS\MessageBundle\EntityManager\ThreadManager as BaseThreadManager;
use Doctrine\ORM\EntityManager;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\Model\ReadableInterface;
use FOS\MessageBundle\Model\ParticipantInterface;
use Doctrine\ORM\Query\Builder;

/**
 * Default ORM ThreadManager.
 *
 */
class ThreadManager extends BaseThreadManager
{
    
    public function getParticipantThreadsQueryBuilder(ParticipantInterface $participant, $limit=null)
    {
        $qb         = $this->repository->createQueryBuilder('t');
        
        $qb->innerJoin('t.metadata', 'tm')
            ->innerJoin('tm.participant', 'p')

            // the participant is in the thread participants
            ->andWhere('p.id = :user_id')
            ->setParameter('user_id', $participant->getId())

            // the thread does not contain spam or flood
            ->andWhere('t.isSpam = :isSpam')
            ->setParameter('isSpam', false, \PDO::PARAM_BOOL)

            // the thread is not deleted by this participant
            ->andWhere('tm.isDeleted = :isDeleted')
            ->setParameter('isDeleted', false, \PDO::PARAM_BOOL)

            // there is at least one message written by an other participant
            //->andWhere('tm.lastMessageDate IS NOT NULL')

            // sort by date of last message written by an other participant
            //->orderBy('tm.lastMessageDate', 'DESC')
                
            ->orderBy('t.createdAt', 'DESC')
        ;
        
        if ($limit!==null){
            $qb->setFirstResult(0);
            $qb->setMaxResults($limit);
        }
        
        return $qb;
    }
    
    public function findParticipantThreads(ParticipantInterface $participant, $limit=null)
    {
        return $this->getParticipantThreadsQueryBuilder($participant, $limit)
            ->getQuery()
            ->execute();
    }
    
}
