<?php
namespace Zizoo\BoatBundle\Validator\Constraints;

use Symfony\Component\Validator\ExecutionContext;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\DependencyInjection\Container;

class BookBoatValidator extends ConstraintValidator
{
    
    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function validate($bookBoat, Constraint $constraint)
    {
        $em                 = $this->container->get('doctrine.orm.entity_manager');
        $reservationAgent   = $this->container->get('zizoo_reservation_reservation_agent');
        $boat               = $em->getRepository('ZizooBoatBundle:Boat')->findOneById($bookBoat->getBoatId());
                
        if ($bookBoat->getNumGuests() > $boat->getNrGuests()){
            $this->context->addViolationAt('num_guests', $constraint->messageNumGuests, array(), null);
        }
        if (!$boat->getCrewOptional() && !$bookBoat->getCrew()){
            $this->context->addViolationAt('crew', $constraint->messageCrew, array(), null);
        }   
                
        $reservationRange   = $bookBoat->getReservationRange();
        if ($reservationRange){
            
            $from               = clone $reservationRange->getReservationFrom();
            $to                 = clone $reservationRange->getReservationTo();
            $from->setTime(0,0,0);
            $to->setTime(0,0,0);
            $interval           = $from->diff($to);
            
            $now = new \DateTime();
            $now->setTime(0,0,0);
            $interval2 = $now->diff($from);
            $minDaysInFuture = $this->container->getParameter('zizoo_reservation.min_reservation_days_in_advance');
            
            if (!$boat->getActive() || $boat->getDeleted()){
                $this->context->addViolationAt('reservation_range', $constraint->messageNotAvailable, array(), null);
            } else if ($from >= $to){
                $this->context->addViolationAt('reservation_range', $constraint->messageNotBookable, array(), null);
            } else if ($reservationAgent->reservationExists($boat, $from, $to) || !$reservationAgent->available($boat, $from, $to)){
                $this->context->addViolationAt('reservation_range', $constraint->messageNotBookable, array(), null);
            } else if ($boat->getMinimumDays() && $interval->days < $boat->getMinimumDays()){
                $this->context->addViolationAt('reservation_range', $constraint->messageMinDays, array('%days%' => $boat->getMinimumDays()), null);
            } else if ($interval2->invert){
                $this->context->addViolationAt('reservation_range', $constraint->messagePast, array(), null);
            } else if ($interval2->days < $minDaysInFuture){
                $this->context->addViolationAt('reservation_range', $constraint->messageMinAdvanceDays, array('%days%' => $minDaysInFuture), null);
            } 
        }
    }
}
?>
