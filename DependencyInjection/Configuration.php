<?php

namespace Wassa\ApiKeyAuthenticatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    // Possible location values
    const LOCATION_HEADERS  = 0b0001;
    const LOCATION_QUERY    = 0b0010;
    const LOCATION_BODY     = 0b0100;
    const LOCATION_PATH     = 0b1000; // Not implemented yet
    const LOCATION_ALL      = 0b1111;

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('wassa_api_key_authenticator');

        $rootNode->children()
            ->scalarNode('name')->defaultValue('x-api-key')->end()
            ->scalarNode('role')->defaultValue('ROLE_API')->end()
            ->scalarNode('location')->defaultValue('all')->end()
            ->scalarNode('key_size')->defaultValue(32)->end()
            ->scalarNode('generator')->defaultValue('wassa_api_key_authenticator.random_generator')->end()
            ->end();

        return $treeBuilder;
    }
}
