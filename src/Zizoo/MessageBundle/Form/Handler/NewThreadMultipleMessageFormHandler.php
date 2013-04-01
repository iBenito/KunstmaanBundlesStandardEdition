<?php
namespace Zizoo\MessageBundle\Form\Handler;

use FOS\MessageBundle\FormHandler\AbstractMessageFormHandler;
use FOS\MessageBundle\FormModel\AbstractMessage;
use FOS\MessageBundle\FormModel\NewThreadMultipleMessage;
use FOS\MessageBundle\Composer\ComposerInterface;
use FOS\MessageBundle\Sender\SenderInterface;
use FOS\MessageBundle\Security\ParticipantProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Container;
/**
 * Form handler for multiple recipients support
 *
 * @author Łukasz Pospiech <zocimek@gmail.com>
 */
class NewThreadMultipleMessageFormHandler extends AbstractMessageFormHandler
{
    
    protected $container;
    
    public function __construct(Request $request, ComposerInterface $composer, SenderInterface $sender, ParticipantProviderInterface $participantProvider, Container $container)
    {
        parent::__construct($request, $composer, $sender, $participantProvider);
        $this->container = $container;
    }
    
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
        
        $messenger  = $this->container->get('messenger');
        
        if (!$messenger->threadAllowed($this->getAuthenticatedParticipant(), $message)){
            throw new \InvalidArgumentException('Not allowed');
        }
        
        $newThread = $this->composer->newThread();
        $newThread
            ->setSubject($message->getSubject())
            ->addRecipients($message->getRecipients())
            ->setSender($this->getAuthenticatedParticipant())
            ->setBody($message->getBody());
        
        $newMessage = $newThread->getMessage();
        $newMessage->setMessageType($message->getMessageType());
        return $newMessage;
    }
}
