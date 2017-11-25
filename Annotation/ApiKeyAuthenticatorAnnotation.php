<?php
/**
 * Created by PhpStorm.
 * User: jwalter
 * Date: 19/11/2017
 * Time: 23:31
 */

namespace Wassa\ApiKeyAuthenticatorBundle\Annotation;

/**
 * Class ApiKeyAuthenticatorAnnotation
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 *
 * @package Wassa\ApiKeyAuthenticatorBundle\Annotation
 */
class ApiKeyAuthenticatorAnnotation
{
    /**
     * Parameter that contains the API key
     *
     * @var string
     */
    public $parameter;
}