<?php

namespace Zizoo\MessageBundle\MessageBuilder;

use FOS\MessageBundle\MessageBuilder\AbstractMessageBuilder;
use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Sender\SenderInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Fluent interface message builder for new thread messages
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class NewThreadMessageBuilder extends AbstractMessageBuilder
{
    /**
     * The thread subject
     *
     * @param  string
     * @return NewThreadMessageBuilder (fluent interface)
     */
    public function setSubject($subject)
    {
        $this->thread->setSubject($subject);

        return $this;
    }

    /**
     * @param  ParticipantInterface
     * @return NewThreadMessageBuilder (fluent interface)
     */
    public function addRecipient(ParticipantInterface $recipient)
    {
        $this->thread->addParticipant($recipient);

        return $this;
    }

    /**
     * @param  Collection $recipients
     * @return NewThreadMessageBuilder
     */
    public function addRecipients(Collection $recipients)
    {
        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }

        return $this;
    }
    
    public function setThreadType($type){
        $this->thread->setThreadType($type);
        return $this;
    }

}
