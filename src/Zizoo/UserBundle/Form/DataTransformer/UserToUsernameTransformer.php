<?php

// src/Zizoo/UserBundle/Form/DataTransformer/UserRegistrationTransformer.php
namespace Zizoo\UserBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;
use Zizoo\UserBundle\Entity\User;

class UserToUsernameTransformer implements DataTransformerInterface
{

    /**
     * @var ObjectManager
     */
    private $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    public function transform($value)
    {
        if (null === $value) {
            return null;
        }
        
        if (!$value instanceof User) {
            throw new UnexpectedTypeException($value, 'Zizoo\UserBundle\Entity\User');
        }

        return $value->getUsername();
        
    }

 
    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }
        
        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }
        
        $user = $this->em->getRepository('ZizooUserBundle:User')->findOneByUsername($value);
        return $user;
    }
}
?>
