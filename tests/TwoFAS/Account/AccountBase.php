<?php

use TwoFAS\Account\Errors;
use TwoFAS\Account\HttpClient\CurlClient;
use TwoFAS\Account\OAuth\Interfaces\TokenStorage;
use TwoFAS\Account\OAuth\TokenType;
use TwoFAS\Account\TwoFAS;

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
     * @return TwoFAS
     */
    protected function getTwoFAs(array $headers = array())
    {
        $tokenStorage = new FilledStorage();
        $twoFAs       = new TwoFAS($tokenStorage, TokenType::wordpress(), $this->addEnvHeaders($headers));
        $twoFAs->setBaseUrl($this->baseUrl);

        return $twoFAs;
    }

    /**
     * @param array $headers
     *
     * @return TwoFAS
     */
    protected function getTwoFASWithRandomKeys(array $headers = array())
    {
        $tokenStorage = new RandomStorage();
        $twoFAs       = new TwoFAS($tokenStorage, TokenType::wordpress(), $this->addEnvHeaders($headers));
        $twoFAs->setBaseUrl($this->baseUrl);

        return $twoFAs;
    }

    /**
     * @param array $headers
     *
     * @return TwoFAS
     */
    protected function getTwoFASWithRevokedKeys(array $headers = array())
    {
        $tokenStorage = new RevokedStorage();
        $twoFAs       = new TwoFAS($tokenStorage, TokenType::wordpress(), $this->addEnvHeaders($headers));
        $twoFAs->setBaseUrl($this->baseUrl);

        return $twoFAs;
    }

    /**
     * @param array $headers
     *
     * @return TwoFAS
     */
    protected function getEmptyTwoFAS(array $headers = array())
    {
        list($twoFAs) = $this->getEmptyTwoFASAndStorage($headers);

        return $twoFAs;
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    protected function getEmptyTwoFASAndStorage(array $headers = array())
    {
        $tokenStorage = new ArrayStorage();
        $twoFAs       = new TwoFAS($tokenStorage, TokenType::wordpress(), $this->addEnvHeaders($headers));
        $twoFAs->setBaseUrl($this->baseUrl);

        return array($twoFAs, $tokenStorage);
    }

    /**
     * @param TokenStorage $tokenStorage
     * @param array        $headers
     *
     * @return TwoFAS
     */
    protected function getEmptyTwoFASWithCustomStorage(TokenStorage $tokenStorage, array $headers = array())
    {
        $twoFAs = new TwoFAS($tokenStorage, TokenType::wordpress(), $this->addEnvHeaders($headers));
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
        return array(
            'error' => array(
                'code' => Errors::USER_INPUT_ERROR,
                'msg'  => $rules
            )
        );
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
            $headers, array('x-forwarded-proto' => 'https')
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
