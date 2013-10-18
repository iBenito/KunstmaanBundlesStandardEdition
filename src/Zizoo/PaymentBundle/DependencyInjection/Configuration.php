<?php

namespace Zizoo\PaymentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('zizoo_payment');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        
        $rootNode
            ->children()
                ->arrayNode('braintree')
                    ->children()
                        ->scalarNode('path')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->end()
                        ->scalarNode('environment')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->end()
                        ->scalarNode('merchant_id')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->end()
                        ->scalarNode('public_key')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->end()
                        ->scalarNode('private_key')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->end()
                        ->scalarNode('client_side_key')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->end()
                    ->end()
                ->end()
            ->end()
        ;
        
        return $treeBuilder;
    }
}
