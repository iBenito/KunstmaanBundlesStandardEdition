<?php

namespace Zizoo\MessageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

/**
 * MessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessageRepository extends EntityRepository
{
    
    public function getInboxMessagesQueryBuilder($profile, $showThreads=false){
        $qb = $this->createQueryBuilder('m')
                                        ->leftJoin('m.recipients', 'r')
                                        ->leftJoin('m.sender_profile', 'sp')
                                        ->leftJoin('sp.user', 'su')
                                        ->select('DISTINCT m.id, r.id as recipient_id, m.subject, CONCAT(sp.lastName, CONCAT(\', \', CONCAT(sp.firstName, CONCAT(\' &lt;\', CONCAT(su.email, \'&gt;\'))))) as sender, m.sent, m.type, r.recipient_read_date as read')
                                        ->where('r.recipient_profile = :recipient')
                                            ->setParameter('recipient', $profile->getID())
                                        ->andWhere('r.recipient_keep = :recipient_keep')
                                            ->setParameter('recipient_keep', true)
                                        ->orderBy('m.id', 'desc');
        if (!$showThreads) {
            $qb->andWhere('(m.reply_to_message IS NULL OR (m.reply_to_message = m.thread_root_message))');
        }
        $qb->getQuery()->getArrayResult();
        return $qb;
    }
    
    public function getSentQueryBuilder($profile, $showThreads=false){
        $qb = $this->createQueryBuilder('m')
                                        ->leftJoin('m.recipients', 'r')
                                        ->leftJoin('r.recipient_profile', 'rp')
                                        ->leftJoin('m.sender_profile', 'sp')
                                        ->leftJoin('rp.user', 'ru')
                                        ->select('m.id as message_id, r.id as recipient_id, m.subject, CONCAT(rp.lastName, CONCAT(\', \', CONCAT(rp.firstName, CONCAT(\' &lt;\', CONCAT(ru.email, \'&gt;\'))))) as receiver, m.sent')
                                        ->where('sp.id = :sender')
                                            ->setParameter('sender', $profile->getID())
                                        ->andWhere('m.sender_keep = :sender_keep')
                                            ->setParameter('sender_keep', true)
                                        ->orderBy('m.id', 'desc');
        if (!$showThreads) {
            $qb->andWhere('(m.reply_to_message IS NULL OR (m.reply_to_message = m.thread_root_message))');
        }
        return $qb;
    }
    
    private function getReply(Message $message){
        $qb = $this->createQueryBuilder('m')
                                        ->select('m')
                                        ->where('m.reply_to_message = :reply_to_message')
                                        ->setParameter('reply_to_message', $message->getId());
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    public function getMessageThread(Message $message){
        /**$previousMessages   = new ArrayCollection();
        $afterMessages      = new ArrayCollection();
        $afterMessages->add($message);
        
        while ( ($previous = $message->getReplyToMessage()) !=null){
            $previousMessages->add($previous);
            $message = $previous;
        }
        $previousMessages = new ArrayCollection(array_reverse($previousMessages->toArray()));
        
        
        while ( ($next = $this->getReply($message)) != null){
            $afterMessages->add($next);
            $message = $next;
        }
        
        return new ArrayCollection(array_merge($previousMessages->toArray(), $afterMessages->toArray()));*/
        $root = $message;
        while ( ($previous = $root->getReplyToMessage()) !=null){
            $root = $previous;
        }
        
        $qb = $this->createQueryBuilder('m')
                    ->select('m')
                    ->where('m.id = :message_id')
                        ->setParameter('message_id', $message->getId())
                    ->orWhere('m.id = :message_id')
                        ->setParameter('message_id', $root->getId())
                    ->orWhere('m.thread_root_message = :root_id')
                        ->setParameter('root_id', $root->getId())
                    ->orderBy('m.id', 'asc');
        
        return $qb->getQuery()->getResult();
    }
    
   
}
