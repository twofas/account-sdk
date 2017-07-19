<?php

use TwoFAS\Account\OAuth\Interfaces\TokenStorage;
use TwoFAS\Account\OAuth\Token;
use TwoFAS\Account\OAuth\TokenNotFoundException;

class RandomStorage implements TokenStorage
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
        if ($type === 'wordpress') {
            return new Token('wordpress', 'abc.def.abc', 0);
        }

        if ($type === 'setup') {
            return new Token('setup', 'abc.def.abc', 0);
        }

        throw new TokenNotFoundException;
    }
}