<?php

namespace TwoFAS\Account;

/**
 * This is an Entity that stores information about integration key.
 *
 * @package TwoFAS\Account
 */
class Key
{
    /**
     * @var string
     */
    private $token;

    /**
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
