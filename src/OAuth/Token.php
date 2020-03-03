<?php

namespace TwoFAS\Account\OAuth;

use InvalidArgumentException;

/**
 * This is an Entity that stores authorization data.
 *
 * @package TwoFAS\Account\OAuth
 */
class Token
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var int
     */
    private $integrationId;

    /**
     * @param string $type
     * @param string $accessToken
     * @param int    $integrationId
     */
    public function __construct($type, $accessToken, $integrationId)
    {
        if (!TokenType::isValid($type)) {
            throw new InvalidArgumentException;
        }

        $this->type          = $type;
        $this->accessToken   = $accessToken;
        $this->integrationId = $integrationId;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return int
     */
    public function getIntegrationId()
    {
        return $this->integrationId;
    }
}
