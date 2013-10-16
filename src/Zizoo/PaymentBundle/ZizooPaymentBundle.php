<?php

namespace Zizoo\PaymentBundle;

use Zizoo\PaymentBundle\DependencyInjection\Compiler\AddPaymentMethodFormTypesPass;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ZizooPaymentBundle extends Bundle
{
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);
        $builder->addCompilerPass(new AddPaymentMethodFormTypesPass());
    }
}
