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
                
        $reservationRange   = $bookBoat->getReservationRange();
        if ($reservationRange){
            $from               = $reservationRange->getReservationFrom();
            $to                 = $reservationRange->getReservationTo();
            $interval           = $from->diff($to);
            if ($from >= $to){
                $this->context->addViolationAt('reservation_range', $constraint->messageNotBookable, array(), null);
            } else if ($reservationAgent->reservationExists($boat, $from, $to) || !$reservationAgent->available($boat, $from, $to)){
                $this->context->addViolationAt('reservation_range', $constraint->messageNotBookable, array(), null);
            } else if ($boat->getMinimumDays() && $interval->days < $boat->getMinimumDays()){
                $this->context->addViolationAt('reservation_range', $constraint->messageMinDays, array('%days%' => $boat->getMinimumDays()), null);
            }
        }
    }
}
?>
