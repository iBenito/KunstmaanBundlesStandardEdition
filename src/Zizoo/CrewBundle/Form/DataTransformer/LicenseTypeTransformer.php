<?php
namespace Zizoo\CrewBundle\Form\DataTransformer;


use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\DataTransformerInterface;

use Zizoo\CrewBundle\Entity\SkillLicense;

class LicenseTypeTransformer implements DataTransformerInterface
{

    /**
     * @var ObjectManager
     */
    private $em;
    
    public function __construct()
    {

    }


    public function transform($value)
    {
        return $value;
    }

 
    public function reverseTransform($value)
    {
        if (!$value instanceof SkillLicense){
            throw new UnexpectedTypeException($value, 'Zizoo\CrewBundle\Entity\SkillLicense');
        }

        if ($value->getId()===null){
            return null;
        }
        return $value;
    }
}
?>
