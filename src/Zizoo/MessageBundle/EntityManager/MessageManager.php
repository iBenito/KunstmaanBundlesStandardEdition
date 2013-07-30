<?php

namespace Zizoo\MessageBundle\EntityManager;

use FOS\MessageBundle\EntityManager\MessageManager as BaseMessageManager;
use Doctrine\ORM\EntityManager;
use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\Model\ReadableInterface;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\ThreadInterface;
use Doctrine\ORM\Query\Builder;

/**
 * Default ORM MessageManager.
 *
 */
class MessageManager extends BaseMessageManager
{
    
    /**
     * Tells how many unread messages this participant has
     *
     * @param ParticipantInterface $participant
     * @return int the number of unread messages
     */
    public function getNbUnreadMessageInThreadByParticipant(ThreadInterface $thread, ParticipantInterface $participant)
    {
        $builder = $this->repository->createQueryBuilder('m');

        return (int)$builder
            ->select($builder->expr()->count('mm.id'))

            ->innerJoin('m.metadata', 'mm')
            ->innerJoin('mm.participant', 'p')

            ->where('p.id = :participant_id')
            ->setParameter('participant_id', $participant->getId())

            ->andWhere('m.sender != :sender')
            ->setParameter('sender', $participant->getId())
                
            ->andWhere('m.thread = :thread')
            ->setParameter('thread', $thread)

            ->andWhere('mm.isRead = :isRead')
            ->setParameter('isRead', false, \PDO::PARAM_BOOL)

            ->getQuery()
            ->getSingleScalarResult();
    }
    
}
