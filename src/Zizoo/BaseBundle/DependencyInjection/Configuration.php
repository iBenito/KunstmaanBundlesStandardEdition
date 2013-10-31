<?php

namespace Zizoo\BaseBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('zizoo_base');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
            ->children()
                ->arrayNode('dashboard_routes')
                    ->children()
                        ->arrayNode('user_routes')
                            ->children()
                                ->scalarNode('profile_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('skills_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('bookings_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('view_booking_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('inbox_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('view_thread_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('account_settings_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('verify_facebook_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('unverify_facebook_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('invite_route')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                        ->end() // end user routes
                        ->arrayNode('charter_routes')
                            ->children()
                                ->scalarNode('profile_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('bookings_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('view_booking_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('accept_booking_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('deny_booking_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('inbox_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('view_thread_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('payments_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('payout_settings_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('invite_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('boats_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('boat_new_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('boat_edit_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('boat_details_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('boat_photos_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('boat_calendar_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('boat_delete_route')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('boat_active_route')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                        ->end() // end charter routes
                    ->end() // end dashboard routes
            ->end()
        ;
        
        return $treeBuilder;
    }
}
