<?php
/**
 * Created by PhpStorm.
 * User: jwalter
 * Date: 17/11/2017
 * Time: 14:29
 */

namespace Wassa\ApiKeyAuthenticatorBundle\Security;


use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Wassa\ApiKeyAuthenticatorBundle\DependencyInjection\Configuration;

class ApiKeyAuthenticator extends AbstractGuardAuthenticator
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function supports(Request $request)
    {
        return true;
    }

    public function getCredentials(Request $request)
    {
        // Check where we need to get the token from
        $location = $this->container->getParameter('apikey_authenticator.location');
        $name = $this->container->getParameter('apikey_authenticator.name');

        // Try in request headers
        if (($location & Configuration::LOCATION_HEADERS) == Configuration::LOCATION_HEADERS) {
            if (($token = $request->headers->get($name))) {
                return ['token' => $token];
            }
        }

        // Try in request query
        if (($location & Configuration::LOCATION_QUERY) == Configuration::LOCATION_QUERY) {
            if (($token = $request->get($name))) {
                return ['token' => $token];
            }
        }

        // Try in request body
        if (($location & Configuration::LOCATION_BODY) == Configuration::LOCATION_BODY) {
            if (($token = $request->request->get($name))) {
                return ['token' => $token];
            }
        }

        // Return null if the API key is not found
        return ['token' => null];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $apiKey = $credentials['token'];

        if (null === $apiKey) {
            return null;
        }

        // if a User object, checkCredentials() is called
        return $userProvider->loadUserByUsername($apiKey);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // If the user doesn't implement ApiKeyUserInterface, return null
        if (!$user instanceof ApiKeyUserInterface) {
            return null;
        }

        // If the provided API key is not the same as the generated one, return null
        if ($credentials['token'] != $user->getApiKey()) {
            return null;
        }

        // return true to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            // you might translate this message
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}