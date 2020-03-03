<?php

namespace TwoFAS\Account\Storage;

use TwoFAS\Account\OAuth\Interfaces\TokenStorage;
use TwoFAS\Account\OAuth\Token;
use TwoFAS\Account\OAuth\TokenNotFoundException;

class ArrayStorage implements TokenStorage
{
    /**
     * @var array
     */
    private $tokens;

    /**
     * EmptyStorage constructor.
     */
    public function __construct()
    {
        $this->tokens = [];
    }

    /**
     * @inheritdoc
     */
    public function storeToken(Token $token)
    {
        $this->tokens[$token->getType()] = $token;
    }

    /**
     * @inheritdoc
     */
    public function retrieveToken($type)
    {
        if (array_key_exists($type, $this->tokens)) {
            return $this->tokens[$type];
        }

        throw new TokenNotFoundException;
    }
}