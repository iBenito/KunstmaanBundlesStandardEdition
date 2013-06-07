<?php
namespace Zizoo\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class InviteSingle extends Constraint
{
    public $message = 'Invalid invitation.';
    
    public function validatedBy()
    {
        return 'validator.invite_single_validator';
    }
    
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
?>
