<?php

// src/Zizoo/BoatBundle/Form/DataTransformer/BoatTypeTransformer.php
namespace Zizoo\BoatBundle\Form\DataTransformer;

use Zizoo\BoatBundle\Entity\BoatType;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;

class BoatTypeTransformer implements DataTransformerInterface
{

    /**
     * @var ObjectManager
     */
    private $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    public function transform($boatType)
    {
        if (null === $boatType) {
            return null;
        }
        
        if (!$boatType instanceof ThreadType) {
            throw new UnexpectedTypeException($value, 'Zizoo\BoatBundle\Entity\BoatType');
        }

        return $boatType->getId();
        
    }

 
    public function reverseTransform($boatTypeInt)
    {
        if (null === $boatTypeInt || '' === $boatTypeInt) {
            return null;
        }
        
        if (!is_integer($boatTypeInt)) {
            throw new UnexpectedTypeException($boatTypeInt, 'integer');
        }
        
        $boatType = $this->em->getRepository('ZizooBoatBundle:BoatType')->findOneById($boatTypeInt);
        return $boatType;
    }
}
?>
