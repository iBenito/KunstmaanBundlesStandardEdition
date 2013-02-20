<?php

/*
* This file is part of the Symfony package.
*
* (c) Fabien Potencier <fabien@symfony.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Zizoo\BillingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
* @author Manuel Reinhard <manu@sprain.ch>
* @author Michael Schummel
* @link http://www.michael-schummel.de/2007/10/05/iban-prufung-mit-php/
*
* @api
*/
class BicValidator extends ConstraintValidator
{

    /**
* {@inheritDoc}
*/
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if(!preg_match("/^([a-zA-Z]){4}([a-zA-Z]){2}([0-9a-zA-Z]){2}([0-9a-zA-Z]{3})?$/", $value))
        {
            $this->context->addViolation($constraint->message, array('{{ value }}' => $value));
            return;
        }
    }
}