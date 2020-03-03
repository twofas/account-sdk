<?php

namespace TwoFAS\Account\OAuth;

use TwoFAS\Account\Exception\Exception;

/**
 * This exception will be thrown if your token storage is empty.
 *
 * @package TwoFAS\Account\OAuth
 */
class TokenNotFoundException extends Exception
{
}