<?php

// src/Zizoo/MessageBundle/Form/DataTransformer/ThreadTypeTransformer.php
namespace Zizoo\MessageBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;
use Zizoo\MessageBundle\Entity\ThreadType;

class ThreadTypeTransformer implements DataTransformerInterface
{

    /**
     * @var ObjectManager
     */
    private $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    public function transform($threadType)
    {
        if (null === $threadType) {
            return null;
        }
        
        if (!$value instanceof ThreadType) {
            throw new UnexpectedTypeException($value, 'Zizoo\UserBundle\Entity\ThreadType');
        }

        return $threadType->getId();
        
    }

 
    public function reverseTransform($threadTypeInt)
    {
        if (null === $threadTypeInt || '' === $threadTypeInt) {
            return null;
        }
        
        if (!is_integer($threadTypeInt)) {
            throw new UnexpectedTypeException($threadTypeInt, 'integer');
        }
        
        $threadType = $this->em->getRepository('ZizooMessageBundle:ThreadType')->findOneById($threadTypeInt);
        return $threadType;
    }
}
?>
