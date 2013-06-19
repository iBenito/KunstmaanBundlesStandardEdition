<?php

namespace Zizoo\BoatBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BookBoat extends Constraint
{
    public $messageNumGuests        = 'zizoo_boat.error.booking_num_guest';
    public $messageNotBookable      = 'zizoo_boat.error.booking_not_available';
    public $messageMinDays          = 'zizoo_boat.error.booking_minimum_days';
    public $messageMinAdvanceDays   = 'zizoo_boat.error.booking_minimum_days_in_advance';
    public $messagePast             = 'zizoo_boat.error.booking_past';
    public $messageCrew             = 'zizoo_boat.error.booking_crew';
    public $messageNotAvailable     = 'zizoo_boat.error.not_available';
    
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
