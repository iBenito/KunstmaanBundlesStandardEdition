<?php

namespace Zizoo\BookingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

class AddPaymentPluginsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('zizoo_booking_booking_agent')) {
            return;
        }

        $bookingAgentDef = $container->findDefinition('zizoo_booking_booking_agent');
        foreach ($container->findTaggedServiceIds('zizoo_payment.plugin') as $id => $attributes) {
            $bookingAgentDef->addMethodCall('addPlugin', array(new Reference($id)));
            
            if (!isset($attributes[0]['jms_payment_plugin'])) {
                throw new \RuntimeException(sprintf('Please define an alias attribute for tag "form.type" of service "%s".', $id));
            }
            $jmsPluginId = $attributes[0]['jms_payment_plugin'];
            
            if (!isset($attributes[0]['processes'])) {
                throw new \RuntimeException(sprintf('Please define an alias attribute for tag "form.type" of service "%s".', $id));
            }
            $processes = $attributes[0]['processes'];
            
            $jmsPluginDef = $container->findDefinition($jmsPluginId);
            
//            $jmsPluginId = 'payment.plugin.'.$id;
//            $jmsPluginDef = $container->register($jmsPluginId, $jmsPluginClass);
//            $jmsPluginDef->addTag('payment.plugin');
//            $container->addDefinitions(array( $jmsPluginId => $jmsPluginDef ));
            
            $pluginDef = $container->getDefinition($id);
            $pluginDef->addMethodCall('setPlugin', array(new Reference($jmsPluginId)));
            $pluginDef->addMethodCall('setId', array($processes));
        }
    }
}