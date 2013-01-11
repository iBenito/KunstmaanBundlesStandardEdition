<?php

namespace Zizoo\BoatBundle\DataFixtures\ORM;

use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\MessageBundle\Entity\Message;
use Zizoo\MessageBundle\Entity\MessageRecipient;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class MessageFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $profile1 = $this->getReference('profile-1');
        $profile2 = $this->getReference('profile-2');
        $profile3 = $this->getReference('profile-3');
        
        $message = new Message();
        $message->setSubject('First message!');
        $message->setBody('This is a test!');
        $message->setSenderProfile($profile2);
        
        $messageRecipient = new MessageRecipient();
        $messageRecipient->setRecipientProfile($profile1);
        $messageRecipient->setMessage($message);
        
        $message->addRecipient($messageRecipient);
        
        $manager->persist($messageRecipient);
        $manager->persist($message);
       
        $message2 = $message->getReplyMessage();
        $message2->setSenderProfile($profile1);
        $message2->setBody("It's working!! I'm copying Sinan in :-)");
        $message2->setThreadRootMessage($message->getId());
        
        $messageRecipient = new MessageRecipient();
        $messageRecipient->setRecipientProfile($profile3);
        $messageRecipient->setMessage($message2);
        $message2->addRecipient($messageRecipient);
        $message2Recipients = $message2->getRecipients();
        foreach ($message2Recipients as $message2Recipient){
            $manager->persist($message2Recipient);
        }
        $manager->persist($message2);
        
        
        $message3 = $message2->getReplyMessage();
        $message3->setSenderProfile($profile2);
        $message3->setBody("Hey Alex, I'm replying just to you.");
        $message3->setThreadRootMessage($message->getId());
        
        $messageRecipient = new MessageRecipient();
        $messageRecipient->setRecipientProfile($profile1);
        $messageRecipient->setMessage($message3);
        $message3->addRecipient($messageRecipient);
        $message3Recipients = $message3->getRecipients();
        foreach ($message3Recipients as $message3Recipient){
            $manager->persist($message3Recipient);
        }
        $manager->persist($message3);
        
        
        $message4 = new Message();
        $message4->setSubject('2nd message thread!');
        $message4->setBody('This is a test!');
        $message4->setSenderProfile($profile1);
        
        $messageRecipient = new MessageRecipient();
        $messageRecipient->setRecipientProfile($profile2);
        $messageRecipient->setMessage($message4);
        
        $message4->addRecipient($messageRecipient);
        
        $manager->persist($messageRecipient);
        $manager->persist($message4);
        
        
        $message5 = $message4->getReplyMessage();
        $message5->setSenderProfile($profile2);
        $message5->setBody("Hey Alex, I'm replying just to you.");
        $message5->setThreadRootMessage($message->getId());
        
        $messageRecipient = new MessageRecipient();
        $messageRecipient->setRecipientProfile($profile1);
        $messageRecipient->setMessage($message5);
        $message5->addRecipient($messageRecipient);
        $message5Recipients = $message5->getRecipients();
        foreach ($message5Recipients as $message5Recipient){
            $manager->persist($message5Recipient);
        }
        $manager->persist($message5);
        
        $manager->flush();

    }

    public function getOrder()
    {
        return 5;
    }

}