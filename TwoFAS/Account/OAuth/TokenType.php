<?php

namespace TwoFAS\Account\OAuth;

use InvalidArgumentException;

class TokenType
{
    const SETUP                  = 'setup';
    const WORDPRESS              = 'wordpress';
    const SYMFONY                = 'symfony';
    const API                    = 'api';
    const PASSWORDLESS_WORDPRESS = 'passwordless-wordpress';

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     */
    private function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return TokenType
     */
    public static function setup()
    {
        return new self(self::SETUP);
    }

    /**
     * @return TokenType
     */
    public static function wordpress()
    {
        return new self(self::WORDPRESS);
    }

    /**
     * @return TokenType
     */
    public static function symfony()
    {
        return new self(self::SYMFONY);
    }

    /**
     * @return TokenType
     */
    public static function passwordlessWordpress()
    {
        return new self(self::PASSWORDLESS_WORDPRESS);
    }

    /**
     * @return TokenType
     */
    public static function api()
    {
        return new self(self::API);
    }

    /**
     * @param string $type
     *
     * @return TokenType
     */
    public static function fromString($type)
    {
        if (!self::isValid($type)) {
            throw new InvalidArgumentException('Token type');
        }

        return new self($type);
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public static function isValid($type)
    {
        return in_array($type, array(
            self::SETUP,
            self::WORDPRESS,
            self::SYMFONY,
            self::API,
            self::PASSWORDLESS_WORDPRESS
        ), true);
    }
}
