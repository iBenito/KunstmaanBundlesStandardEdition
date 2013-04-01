<?php

// src/Zizoo/MessageBundle/Form/DataTransformer/ThreadTypeTransformer.php
namespace Zizoo\MessageBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;
use Zizoo\MessageBundle\Entity\MessageType;

class MessageTypeTransformer implements DataTransformerInterface
{

    /**
     * @var ObjectManager
     */
    private $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    public function transform($messageType)
    {
        if (null === $messageType) {
            return null;
        }
        
        if (!$messageType instanceof MessageType) {
            throw new UnexpectedTypeException($value, 'Zizoo\MessageBundle\Entity\MessageType');
        }

        return $messageType->getId();
        
    }

 
    public function reverseTransform($messageTypeId)
    {
        if (null === $messageTypeId || '' === $messageTypeId) {
            return null;
        }
        
        if (!is_string($messageTypeId)) {
            throw new UnexpectedTypeException($messageTypeId, 'string');
        }
        
        $messageType = $this->em->getRepository('ZizooMessageBundle:MessageType')->findOneById($messageTypeId);
        return $messageType;
    }
}
?>
