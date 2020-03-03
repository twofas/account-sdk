<?php

namespace TwoFAS\Account\OAuth\Interfaces;

use TwoFAS\Account\OAuth\Token;
use TwoFAS\Account\OAuth\TokenNotFoundException;

/**
 * This interface should be implemented for storing and retrieving tokens used for authorization.
 *
 * @package TwoFAS\Account\OAuth\Interfaces
 */
interface TokenStorage
{
    /**
     * Store token in storage so it can be retrieved for future use
     *
     * @param Token $token
     */
    public function storeToken(Token $token);

    /**
     * Retrieve stored Token object.
     *
     * @param string $type
     *
     * @return Token
     *
     * @throws TokenNotFoundException
     */
    public function retrieveToken($type);
}
