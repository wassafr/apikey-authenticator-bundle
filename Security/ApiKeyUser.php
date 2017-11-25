<?php
/**
 * Created by PhpStorm.
 * User: jwalter
 * Date: 17/11/2017
 * Time: 14:11
 */

namespace Wassa\ApiKeyAuthenticatorBundle\Security;


use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiKeyUser implements UserInterface, EquatableInterface, ApiKeyUserInterface
{
    private $username;
    private $roles;

    public function __construct($username)
    {
        $this->username = $username;
        $this->roles = [];
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {}

    public function getSalt()
    {}

    public function getUsername()
    {
        return $this->username;
    }

    public function getApiKey()
    {
        return $this->username;
    }

    public function addRole($role)
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof ApiKeyUser) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}