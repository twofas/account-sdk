<?php

namespace TwoFAS\Account\Response;

use TwoFAS\Account\Errors;
use TwoFAS\Account\Exception\AuthorizationException;
use TwoFAS\Account\Exception\Exception;
use TwoFAS\Account\Exception\NotFoundException;
use TwoFAS\Account\Exception\PasswordResetAttemptsRemainingIsReachedException;
use TwoFAS\Account\Exception\ValidationException;
use TwoFAS\Account\HttpCodes;

class Response
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var integer
     */
    private $code;

    /**
     * @param array   $data
     * @param integer $code
     */
    public function __construct(array $data, $code)
    {
        $this->data = $data;
        $this->code = $code;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return Exception
     */
    public function getError()
    {
        if ($this->matchesHttpAndErrorCode(HttpCodes::BAD_REQUEST, Errors::USER_INPUT_ERROR)) {
            return new ValidationException($this->getErrorMessage());
        }

        if ($this->isAuthorizationError()) {
            return new AuthorizationException((string) $this->getErrorMessage());
        }

        if ($this->matchesHttpAndErrorCode(HttpCodes::NOT_FOUND, Errors::MODEL_NOT_FOUND)) {
            return new NotFoundException((string) $this->getErrorMessage());
        }

        if ($this->matchesHttpAndErrorCode(HttpCodes::FORBIDDEN, Errors::PASSWORD_RESET_ATTEMPTS_LIMIT)) {
            return new PasswordResetAttemptsRemainingIsReachedException(
                (string) $this->getErrorMessage(),
                $this->getErrorCode(),
                null,
                $this->getErrorPayloadByKey('minutes_to_next_reset')
            );
        }

        if ($this->hasErrorMessage()) {
            return new Exception('Unsupported response, original message: ' . (string) $this->getErrorMessage());
        }

        return new Exception('Unsupported response');
    }

    /**
     * @param integer $httpCode
     * @param integer $errorCode
     *
     * @return bool
     */
    public function matchesHttpAndErrorCode($httpCode, $errorCode)
    {
        return $this->matchesHttpCode($httpCode)
            && $this->matchesErrorCode($errorCode);
    }

    /**
     * @param integer $httpCode
     *
     * @return bool
     */
    public function matchesHttpCode($httpCode)
    {
        return $this->code === $httpCode;
    }

    /**
     * @param integer $errorCode
     *
     * @return bool
     */
    public function matchesErrorCode($errorCode)
    {
        return isset($this->data['error']['code'])
            && $errorCode === $this->data['error']['code'];
    }

    private function isAuthorizationError()
    {
        return $this->matchesHttpAndErrorCode(HttpCodes::UNAUTHORIZED, Errors::UNAUTHORIZED);
    }

    /**
     * @return bool
     */
    private function hasErrorMessage()
    {
        return !empty($this->data['error']['msg']);
    }

    /**
     * @return string|array
     */
    private function getErrorMessage()
    {
        return $this->data['error']['msg'];
    }

    /**
     * @return int
     */
    private function getErrorCode()
    {
        return $this->data['error']['code'];
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function getErrorPayloadByKey($key)
    {
        if (!array_key_exists($key, $this->data['error']['payload'])) {
            return '';
        }

        return (string) $this->data['error']['payload'][$key];
    }
}
