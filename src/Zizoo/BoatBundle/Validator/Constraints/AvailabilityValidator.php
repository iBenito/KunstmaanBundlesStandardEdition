<?php
namespace Zizoo\BoatBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AvailabilityValidator extends ConstraintValidator
{
    
    public function validate($availabilty, Constraint $constraint)
    {
        // Type
        $type = $availabilty->getType();
        if ($type === null){
            $this->context->addViolationAt('type', $constraint->messageSelectType, array(), null);
        } else if ($type!='availability' && $type!='default' && $type!='unavailability'){
            $this->context->addViolationAt('type', $constraint->messageInvalidType, array(), null);
        }
        
        // Date range
        $reservationRange   = $availabilty->getReservationRange();
        if ($reservationRange){
            $from               = $reservationRange->getReservationFrom();
            $to                 = $reservationRange->getReservationTo();
            if ($from instanceof \DateTime && $to instanceof \DateTime){
                $now = new \DateTime();
                $now->setTime(0,0,0);
                $from->setTime(0,0,0);
                $to->setTime(0,0,0);
                if ($from < $now){
                    $this->context->addViolationAt('reservation_range', $constraint->messageDatesPast, array(), null);
                } else {
                    if ($from > $to && ($type==='availability' || $type==='default')){
                        $this->context->addViolationAt('reservation_range', $constraint->messageDatesDefaultOrAvailable, array(), null);
                    } else if ($from >= $to  && $type!=='availability' && $type!=='default'){
                        $this->context->addViolationAt('reservation_range', $constraint->messageDatesNotDefaultAndNotAvailable, array(), null);
                    }
                }
                $from->setTime(12,0,0);
                $to->setTime(11,59,59);
            }
        }
 
        
        // Price
        $price = $availabilty->getPrice();
        if ($type == 'availability' && $price === null){
            $this->context->addViolationAt('price', $constraint->messagePrice, array(), null);
        }

        $overlappingBookedReservations      = $availabilty->getOverlappingBookedReservations();
        if (count($overlappingBookedReservations)>0){
            $this->context->addViolationAt('reservation_range', $constraint->messageOverlapBookings, array(), null);
        } else {
            $overlappingReservationsRequests    = $availabilty->getOverlappingReservationRequests();
            $overlappingExternalRequests        = $availabilty->getOverlappingExternalReservations();
            $confirmed                          = $availabilty->getConfirm();
            if ($confirmed == false && ( count($overlappingExternalRequests)>0 || count($overlappingReservationsRequests)>0) ){
                $this->context->addViolationAt('deny_reservation', $constraint->messageOverlapReservations, array(), null);
            }
        }
        
    }
}
?>
