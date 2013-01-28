<?php
// src/Zizoo/MessageBundle/EventListener/MessageEventListener.php
namespace Zizoo\MessageBundle\EventListener;

use Zizoo\MessageBundle\Entity\Contact;
use Zizoo\MessageBundle\Service\Messenger;
use Zizoo\UserBundle\Entity\User;

use FOS\MessageBundle\Event\MessageEvent;
use FOS\MessageBundle\Event\ThreadEvent;
use FOS\MessageBundle\Event\FOSMessageEvents;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;

class MessageEventListener
{
    
    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function onMessageEvent(MessageEvent $event)
    {
        $em         = $this->container->get('doctrine.orm.entity_manager');
        $messenger  = $this->container->get('messenger');
        $message    = $event->getMessage();
        
        $sender     = $message->getSender();
        $thread     = $message->getThread();
        $recipients = $thread->getOtherParticipants($sender);
                
        foreach ($recipients as $recipient){
            $senderContact = $em->getRepository('ZizooMessageBundle:Contact')->findOneBy( array(    'sender'    => $sender,
                                                                                                    'recipient' => $recipient) );
            if (!$senderContact){
                $senderContact = new Contact();
                $senderContact->setSender($sender);
                $senderContact->setRecipient($recipient);
                $em->persist($senderContact);
            }
            
            $recipientContact = $em->getRepository('ZizooMessageBundle:Contact')->findOneBy( array( 'sender'    => $recipient,
                                                                                                    'recipient' => $sender) );
            if (!$recipientContact){
                $recipientContact = new Contact();
                $recipientContact->setSender($recipient);
                $recipientContact->setRecipient($sender);
                $em->persist($recipientContact);
            }
            
        }        
        
        $em->flush();
    }
}
?>
