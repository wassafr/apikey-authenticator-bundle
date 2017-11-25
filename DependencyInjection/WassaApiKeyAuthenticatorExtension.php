<?php

namespace Wassa\ApiKeyAuthenticatorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class WassaApiKeyAuthenticatorExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('apikey_authenticator.name', $config['name']);
        $container->setParameter('apikey_authenticator.role', $config['role']);
        $container->setParameter('apikey_authenticator.key_size', $config['key_size']);
        $container->setParameter('apikey_authenticator.generator', $config['generator']);

        // Configure the location (where to search in the request) of the API key
        // Parse the 'location' option
        $locations = explode('&', $config['location']);
        $allLocations = 0;

        foreach ($locations as $location) {
            switch ($location) {
                case 'all':
                    $allLocations = Configuration::LOCATION_ALL;
                    break;

                case 'headers':
                    $allLocations |= Configuration::LOCATION_HEADERS;
                    break;

                case 'query':
                    $allLocations |= Configuration::LOCATION_QUERY;
                    break;

                case 'body':
                    $allLocations |= Configuration::LOCATION_BODY;
                    break;

                case 'path':
                    $allLocations |= Configuration::LOCATION_PATH;
                    break;

                default:
                    $allLocations = Configuration::LOCATION_ALL;
                    break;
            }
        }

        $container->setParameter('apikey_authenticator.location', $allLocations);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
