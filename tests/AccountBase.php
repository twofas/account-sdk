<?php

namespace TwoFAS\Account;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use TwoFAS\Account\HttpClient\CurlClient;
use TwoFAS\Account\OAuth\Interfaces\TokenStorage;
use TwoFAS\Account\OAuth\TokenType;
use TwoFAS\Account\Storage\ArrayStorage;
use TwoFAS\Account\Storage\FilledStorage;
use TwoFAS\Account\Storage\RandomStorage;
use TwoFAS\Account\Storage\RevokedStorage;

abstract class AccountBase extends PHPUnit_Framework_TestCase
{
    private $env;
    private $baseUrl;

    protected function setUp()
    {
        parent::setUp();

        $this->env     = getenv('env');
        $this->baseUrl = getenv('base_url');
    }

    /**
     * @param array $headers
     *
     * @return Sdk
     */
    protected function getTwoFAs(array $headers = [])
    {
        $tokenStorage = new FilledStorage();
        $twoFAs       = new Sdk($tokenStorage, TokenType::wordpress(), $this->addEnvHeaders($headers));
        $twoFAs->setBaseUrl($this->baseUrl);

        return $twoFAs;
    }

    /**
     * @param array $headers
     *
     * @return Sdk
     */
    protected function getTwoFASWithRandomKeys(array $headers = [])
    {
        $tokenStorage = new RandomStorage();
        $twoFAs       = new Sdk($tokenStorage, TokenType::wordpress(), $this->addEnvHeaders($headers));
        $twoFAs->setBaseUrl($this->baseUrl);

        return $twoFAs;
    }

    /**
     * @param array $headers
     *
     * @return Sdk
     */
    protected function getTwoFASWithRevokedKeys(array $headers = [])
    {
        $tokenStorage = new RevokedStorage();
        $twoFAs       = new Sdk($tokenStorage, TokenType::wordpress(), $this->addEnvHeaders($headers));
        $twoFAs->setBaseUrl($this->baseUrl);

        return $twoFAs;
    }

    /**
     * @param array $headers
     *
     * @return Sdk
     */
    protected function getEmptyTwoFAS(array $headers = [])
    {
        list($twoFAs) = $this->getEmptyTwoFASAndStorage($headers);

        return $twoFAs;
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    protected function getEmptyTwoFASAndStorage(array $headers = [])
    {
        $tokenStorage = new ArrayStorage();
        $twoFAs       = new Sdk($tokenStorage, TokenType::wordpress(), $this->addEnvHeaders($headers));
        $twoFAs->setBaseUrl($this->baseUrl);

        return [$twoFAs, $tokenStorage];
    }

    /**
     * @param TokenStorage $tokenStorage
     * @param array        $headers
     *
     * @return Sdk
     */
    protected function getEmptyTwoFASWithCustomStorage(TokenStorage $tokenStorage, array $headers = [])
    {
        $twoFAs = new Sdk($tokenStorage, TokenType::wordpress(), $this->addEnvHeaders($headers));
        $twoFAs->setBaseUrl($this->baseUrl);

        return $twoFAs;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|CurlClient
     */
    protected function getHttpClient()
    {
        if ($this->isDevelopmentEnvironment()) {
            return $this->getMockBuilder('\TwoFAS\Account\HttpClient\CurlClient')->getMock();
        }

        return new CurlClient();
    }

    /**
     * @param array $rules
     *
     * @return array
     */
    protected function getExpectedValidationBody(array $rules)
    {
        return [
            'error' => [
                'code' => Errors::USER_INPUT_ERROR,
                'msg'  => $rules
            ]
        ];
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    private function addEnvHeaders(array $headers)
    {
        if ($this->isDevelopmentEnvironment()) {
            return $headers;
        }

        return array_merge(
            $headers, ['x-forwarded-proto' => 'https']
        );
    }

    /**
     * @return bool
     */
    protected function isDevelopmentEnvironment()
    {
        return 'dev' === $this->env;
    }
}
