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
        
        $manager->flush();

    }

    public function getOrder()
    {
        return 5;
    }

}