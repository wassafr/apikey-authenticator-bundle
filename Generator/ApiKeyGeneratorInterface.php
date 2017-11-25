<?php
/**
 * Created by PhpStorm.
 * User: jwalter
 * Date: 18/11/2017
 * Time: 23:53
 */

namespace Wassa\ApiKeyAuthenticatorBundle\Generator;


interface ApiKeyGeneratorInterface
{
    /**
     * Generate an API key
     *
     * @param mixed $_
     *
     * @return string the generated API key
     */
    public function generate($_ = null);
}