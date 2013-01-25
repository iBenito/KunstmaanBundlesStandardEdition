<?php
namespace Zizoo\MessageBundle\Form\Handler;

use FOS\MessageBundle\FormHandler\AbstractMessageFormHandler;
use FOS\MessageBundle\FormModel\AbstractMessage;
use FOS\MessageBundle\FormModel\NewThreadMultipleMessage;
/**
 * Form handler for multiple recipients support
 *
 * @author Łukasz Pospiech <zocimek@gmail.com>
 */
class NewThreadMultipleMessageFormHandler extends AbstractMessageFormHandler
{
    /**
     * Composes a message from the form data
     *
     * @param AbstractMessage $message
     *
     * @return MessageInterface the composed message ready to be sent
     * @throws \InvalidArgumentException if the message is not a NewThreadMessage
     */
    public function composeMessage(AbstractMessage $message)
    {
        if (!$message instanceof NewThreadMultipleMessage) {
            throw new \InvalidArgumentException(sprintf('Message must be a NewThreadMultipleMessage instance, "%s" given', get_class($message)));
        }

        $newThread = $this->composer->newThread();
        $newThread
            ->setSubject($message->getSubject())
            ->addRecipients($message->getRecipients())
            ->setSender($this->getAuthenticatedParticipant())
            ->setBody($message->getBody())
            ->setThreadType($message->getThreadType())
            ->getMessage();
        
        return $newThread->getMessage();
    }
}
