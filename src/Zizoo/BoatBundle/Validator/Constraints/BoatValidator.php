<?php
namespace Zizoo\BoatBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BoatValidator extends ConstraintValidator
{
    
    public function validate($boat, Constraint $constraint)
    {   
        if ($boat->getHasDefaultPrice()){
            if (!$boat->getDefaultPrice()){
                $this->context->addViolationAt('defaultPrice', $constraint->messageDefaultPrice, array(), null);
            } else if (!is_float($boat->getDefaultPrice())){
                $this->context->addViolationAt('defaultPrice', $constraint->messageDefaultPrice, array(), null);
            }
        }
        
        if ($boat->getHasMinimumDays()){
            if (!$boat->getMinimumDays()){
                $this->context->addViolationAt('minimumDays', $constraint->messageMinDays, array(), null);
            }else if (!is_int($boat->getMinimumDays())){
                $this->context->addViolationAt('minimumDays', $constraint->messageMinDays, array(), null);
            }
        }
        
        if ($boat->getCrewOptional()){
            if (!$boat->getCrewPrice()){
                $this->context->addViolationAt('crewPrice', $constraint->messageCrewPrice, array(), null);
            }
            if (!is_float($boat->getCrewPrice())){
                $this->context->addViolationAt('crewPrice', $constraint->messageCrewPrice, array(), null);
            }   
        }
    }
}
?>
