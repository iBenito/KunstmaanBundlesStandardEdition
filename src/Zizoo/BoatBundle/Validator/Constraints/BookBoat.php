<?php

namespace Zizoo\BoatBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BookBoat extends Constraint
{
    public $messageNumGuests    = 'This boat cannot handle so many guests.';
    public $messageNotBookable  = 'This boat is not bookable for these dates.';
    
    public function validatedBy()
    {
        return 'validator.book_boat_validator';
    }
    
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
?>
