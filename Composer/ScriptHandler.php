<?php
/**
 * Created by PhpStorm.
 * User: jwalter
 * Date: 18/11/2017
 * Time: 20:11
 */

namespace Wassa\ApiKeyAuthenticatorBundle\Composer;


use Composer\Script\Event;

/**
 * Handles the composer post install script
 *
 * Class ScriptHandler
 *
 * @package Wassa\ApiKeyAuthenticatorBundle\Composer
 */
class ScriptHandler extends \Sensio\Bundle\DistributionBundle\Composer\ScriptHandler
{
    public static function generateApiKey(Event $event)
    {
        // Get the value of 'apikey-size' extra if it exists
        $extra = $event->getComposer()->getPackage()->getExtra();
        // Set the API key size if it was provided in the extra
        $arg = isset($extra['apikey-size']) && (int)$extra['apikey-size'] ? (' -s ' . $extra['apikey-size']) : '';

        $consoleDir = static::getConsoleDir($event, 'clear the cache');

        if (null === $consoleDir) {
            return;
        }

        // Execute the apikeyauthenticator:create-key command
        static::executeCommand($event, $consoleDir, 'apikey-authenticator:create-key' . $arg);
    }
}