<?php

namespace Zizoo\SmsBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('zizoo_sms');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
            ->children()
                ->arrayNode('twilio')
                    ->children()
                        ->scalarNode('environment')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->end()
                        ->arrayNode('sandbox')
                            ->children()
                                ->scalarNode('number')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->end()
                                ->scalarNode('sid')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->end()
                                ->scalarNode('token')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->end()
                            ->end()
                        ->end()
                        ->arrayNode('production')
                            ->children()
                                ->scalarNode('number')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->end()
                                ->scalarNode('sid')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->end()
                                ->scalarNode('token')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
