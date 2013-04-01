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
            $this->context->addViolationAtSubPath('num_guests', $constraint->messageNumGuests, array(), null);
        }
                
        $from   = $bookBoat->getReservationFrom();
        $to     = $bookBoat->getReservationTo();
        if ($from && $to){
            if ($from >= $to){
                $this->context->addViolationAtSubPath('reservation_from', $constraint->messageNotBookable, array(), null);
                $this->context->addViolationAtSubPath('reservation_to', $constraint->messageNotBookable, array(), null);
            } else if ($reservationAgent->reservationExists($boat, $from, $to) || !$reservationAgent->available($boat, $from, $to)){
                $this->context->addViolationAtSubPath('reservation_from', $constraint->messageNotBookable, array(), null);
                $this->context->addViolationAtSubPath('reservation_to', $constraint->messageNotBookable, array(), null);
            }
        }
    }
}
?>
