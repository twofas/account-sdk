<?php

use TwoFAS\Account\OAuth\Interfaces\TokenStorage;
use TwoFAS\Account\OAuth\Token;
use TwoFAS\Account\OAuth\TokenNotFoundException;

class RevokedStorage implements TokenStorage
{
    /**
     * @inheritdoc
     */
    public function storeToken(Token $token) {
        throw new \LogicException();
    }

    /**
     * @inheritdoc
     */
    public function retrieveToken($type) {
        if ($type === 'setup') {
            return new Token('setup', getenv('oauth_setup_revoked_token'), 0);
        }

        throw new TokenNotFoundException;
    }
}