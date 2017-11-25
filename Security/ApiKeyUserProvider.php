<?php
/**
 * Created by PhpStorm.
 * User: jwalter
 * Date: 17/11/2017
 * Time: 14:09
 */

namespace Wassa\ApiKeyAuthenticatorBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiKeyUserProvider implements UserProviderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function loadUserByUsername($username)
    {
        // Load the API key for the key file
        $kernel = $this->container->get('kernel');
        $rootDir = $kernel->getRootDir();
        $apiKeyFile = $rootDir . '/../var/private/api.key';
        $apiKey = file_get_contents($apiKeyFile);

        // Create a user with the loaded API key and assign the configured role
        $user = new ApiKeyUser($apiKey);
        $user->addRole($this->container->getParameter('apikey_authenticator.role'));

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof ApiKeyUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return ApiKeyUser::class === $class;
    }
}