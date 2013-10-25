<?php

namespace Zizoo\MessageBundle\Provider;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\MessageBundle\Provider\Provider as BaseProvider;
use FOS\MessageBundle\ModelManager\ThreadManagerInterface;
use FOS\MessageBundle\Security\AuthorizerInterface;
use FOS\MessageBundle\Reader\ReaderInterface;
use FOS\MessageBundle\Security\ParticipantProviderInterface;
use FOS\MessageBundle\ModelManager\MessageManagerInterface;

/**
 * Provides threads for the current authenticated user
 *
 */
class Provider extends BaseProvider
{
    /**
     *
     * @return array of ThreadInterface
     */
    public function getThreads($limit=null)
    {
        $participant = $this->getAuthenticatedParticipant();

        return $this->threadManager->findParticipantThreads($participant, $limit);
    }
    
    public function getThreadsQueryBuilder($limit=null)
    {
        $participant = $this->getAuthenticatedParticipant();

        return $this->threadManager->getParticipantThreadsQueryBuilder($participant, $limit);
    }
    
}
