<?php

namespace Zizoo\BoatBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Availability extends Constraint
{
    public $messageDatesPast                        = 'zizoo_boat.error.availability_dates_not_past';
    public $messageDatesDefaultOrAvailable          = 'zizoo_boat.error.availability_dates_default_or_available';
    public $messageDatesNotDefaultAndNotAvailable   = 'zizoo_boat.error.availability_dates_not_default_and_not_available';
    public $messageSelectType                       = 'zizoo_boat.error.availability_select_type';
    public $messageInvalidType                      = 'zizoo_boat.error.availability_invalid_type';
    public $messagePrice                            = 'zizoo_boat.error.availability_price';
    public $messageOverlapReservations              = 'zizoo_boat.error.availability_overlap_reservations';
    public $messageOverlapBookings                  = 'zizoo_boat.error.availability_overlap_bookings';
    
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

