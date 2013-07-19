<?php
namespace Zizoo\BoatBundle\Validator\Constraints;

use Symfony\Component\Validator\ExecutionContext;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\DependencyInjection\Container;

class AvailabilityValidator extends ConstraintValidator
{
    
    public function validate($availabilty, Constraint $constraint)
    {
        // Date range
        $reservationRange   = $availabilty->getReservationRange();
        if ($reservationRange){
            $from               = $reservationRange->getReservationFrom();
            $to                 = $reservationRange->getReservationTo();
            if ($from instanceof \DateTime && $to instanceof \DateTime){
                if ($from > $to){
                    $this->context->addViolationAt('reservation_range', $constraint->messageDates, array(), null);
                } 
            }
        }
        
        // Type
        $type = $availabilty->getType();
        if ($type === null){
            $this->context->addViolationAt('type', $constraint->messageSelectType, array(), null);
        } else if ($type!='availability' && $type!='default' && $type!='unavailability'){
            $this->context->addViolationAt('type', $constraint->messageInvalidType, array(), null);
        }
        
        // Price
        $price = $availabilty->getPrice();
        if ($type == 'availability' && $price === null){
            $this->context->addViolationAt('price', $constraint->messagePrice, array(), null);
        }
    }
}
?>
