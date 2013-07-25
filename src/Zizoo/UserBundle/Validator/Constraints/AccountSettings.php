<?php
namespace Zizoo\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class AccountSettings extends Constraint
{
    public $message = 'Invalid invitation.';
    
    public function validatedBy()
    {
        return 'validator.account_settings_validator';
    }
    
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
?>
