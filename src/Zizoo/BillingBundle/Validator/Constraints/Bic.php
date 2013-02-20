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

/**
* @Annotation
*
* @api
*/
class Bic extends Constraint
{
    public $message = 'This is not a valid SWIFT Bank Identifier Code (BIC).';
}
?>
