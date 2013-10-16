<?php

namespace Zizoo\BookingBundle;

use Zizoo\BookingBundle\DependencyInjection\Compiler\AddPaymentPluginsPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ZizooBookingBundle extends Bundle
{
    
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);

        $builder->addCompilerPass(new AddPaymentPluginsPass());
    }
}
