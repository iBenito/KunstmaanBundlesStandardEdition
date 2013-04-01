<?php

namespace Zizoo\MessageBundle\Form\Handler;

use Zizoo\MessageBundle\Form\Model\ReplyMessage;

use FOS\MessageBundle\FormHandler\AbstractMessageFormHandler;
use FOS\MessageBundle\FormModel\AbstractMessage;

class ReplyMessageFormHandler extends AbstractMessageFormHandler
{
    /**
     * Composes a message from the form data
     *
     * @param AbstractMessage $message
     * @return MessageInterface the composed message ready to be sent
     * @throws InvalidArgumentException if the message is not a ReplyMessage
     */
    public function composeMessage(AbstractMessage $message)
    {
        if (!$message instanceof ReplyMessage) {
            throw new \InvalidArgumentException(sprintf('Message must be a ReplyMessage instance, "%s" given', get_class($message)));
        }
        $thread = $message->getThread();
        //$lastMessage = $thread->getLastMessage();
        //$messageType = $lastMessage->getMessageType();
        return $this->composer->reply($thread)
            ->setSender($this->getAuthenticatedParticipant())
            ->setBody($message->getBody())
            ->getMessage();
    }
}
