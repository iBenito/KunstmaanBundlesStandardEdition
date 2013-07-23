<?php

namespace Zizoo\BoatBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Availability extends Constraint
{
    public $messageDates        = 'zizoo_boat.error.availability_dates';
    public $messageSelectType   = 'zizoo_boat.error.availability_select_type';
    public $messageInvalidType  = 'zizoo_boat.error.availability_invalid_type';
    public $messagePrice        = 'zizoo_boat.error.availability_price';
    public $messageOverlap      = 'zizoo_boat.error.availability_overlap';
    
    public function validatedBy()
    {
        return 'validator.availability_validator';
    }
    
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
?>

