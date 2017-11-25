<?php
/**
 * Created by PhpStorm.
 * User: jwalter
 * Date: 18/11/2017
 * Time: 23:58
 */

namespace Wassa\ApiKeyAuthenticatorBundle\Generator;


use Symfony\Component\DependencyInjection\ContainerInterface;

class RandomApiKeyGenerator implements ApiKeyGeneratorInterface
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Generate a random API Key.
     *
     * @param int $size
     *
     * @return string the randomly generated API key
     */
    public function generate($size = null)
    {
        if (!$size) {
            $size = $this->container->getParameter('apikey_authenticator.key_size');
        }

        //  I, 1, O and 0 are excluded for the sake of readability.
        $availableChars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $availableCharsCount = strlen($availableChars) - 1;
        $apiKey = '';

        for ($i = 0; $i < $size; $i++) {
            $apiKey .= $availableChars[rand(0, $availableCharsCount)];
        }

        return $apiKey;
    }
}