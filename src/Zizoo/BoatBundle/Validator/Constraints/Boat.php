<?php

namespace Zizoo\BoatBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Boat extends Constraint
{
    public $messageNumCrew          = 'zizoo_boat.error.num_crew';
    public $messageCrewPrice        = 'zizoo_boat.error.crew_price';
    public $messageDefaultPrice     = 'zizoo_boat.error.default_price';
    public $messageMinDays          = 'zizoo_boat.error.min_days';
    
    public function validatedBy()
    {
        return 'validator.boat_validator';
    }
    
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
?>
