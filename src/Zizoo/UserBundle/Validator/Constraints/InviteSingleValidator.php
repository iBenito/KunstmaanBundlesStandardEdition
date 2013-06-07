<?php


namespace Zizoo\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\DependencyInjection\Container;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EmailValidator;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotBlankValidator;

class InviteSingleValidator extends ConstraintValidator
{

    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function validate($inviteSingle, Constraint $constraint)
    {
        $em                 = $this->container->get('doctrine.orm.entity_manager');
        $userRepo           = $em->getRepository('ZizooUserBundle:User');
        $validator          = $this->container->get('validator');
        $trans              = $this->container->get('translator');
        
        $emailConstraint            = new Email();
        $emailConstraint->checkMX   = true;
        $emailConstraint->checkHost = true;
        
        // Validate email
        $emailValidator             = new EmailValidator();
        $emailValidator->initialize($this->context);
        $emailValidator->validate($inviteSingle->getEmail(), $emailConstraint);
        $violations = $emailValidator->context->getViolations();
        foreach ($violations as $violation){
            //$this->context->addViolationAt($this->context->getPropertyPath(), $violation->getMessage(), array(), null);
        }
        
        $inviteUser = $userRepo->findOneByEmail($inviteSingle->getEmail());
        if ($inviteUser){
            $this->context->addViolationAt('email', $trans->trans('zizoo_user.friend_already_exists'), array(), null);
        }

        //$this->context->addViolationAt('reservation_range', $constraint->messageNotBookable, array(), null);
    }
}