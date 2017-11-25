<?php
/**
 * Created by PhpStorm.
 * User: jwalter
 * Date: 19/11/2017
 * Time: 01:40
 */

namespace Wassa\ApiKeyAuthenticatorBundle\Security;


use Symfony\Component\Security\Core\User\UserInterface;

interface ApiKeyUserInterface extends UserInterface
{
    /**
     * Return the API key
     *
     * @return string
     */
    public function getApiKey();
}