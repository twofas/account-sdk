<?php

use TwoFAS\Account\Errors;
use TwoFAS\Account\Exception\ValidationException;
use TwoFAS\Account\HttpCodes;
use TwoFAS\Account\Response\ResponseGenerator;
use TwoFAS\Account\Exception\Exception as AccountException;

class AuthorisationExceptionsTest extends AccountBase
{
    /**
     * @throws AccountException
     * @throws ValidationException
     */
    public function testCreateIntegrationWithoutKey()
    {
        $twoFAs     = $this->getEmptyTwoFAS();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        $this->setExpectedException('\TwoFAS\Account\OAuth\TokenNotFoundException', '');

        $twoFAs->createIntegration('Unauthorized integration');
    }

    /**
     * @throws ValidationException
     * @throws AccountException
     */
    public function testCallMethodWhichRequiresAuthenticationWithRandomKey()
    {
        $twoFAs     = $this->getTwoFASWithRandomKeys();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = array(
                'error' => array(
                    'code' => Errors::UNAUTHORIZED,
                    'msg'  => 'Unauthorized'
                )
            );

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($response), HttpCodes::UNAUTHORIZED));
        }

        $this->setExpectedException('\TwoFAS\Account\Exception\AuthorizationException', 'Unauthorized');

        $twoFAs->createIntegration('Unauthorized integration');
    }

    /**
     * @throws ValidationException
     * @throws AccountException
     */
    public function testCallMethodWhichRequiresAuthenticationWithRevokedKey()
    {
        $twoFAs     = $this->getTwoFASWithRevokedKeys();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = array(
                'error' => array(
                    'code' => Errors::UNAUTHORIZED,
                    'msg'  => 'Unauthorized'
                )
            );

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($response), HttpCodes::UNAUTHORIZED));
        }

        $this->setExpectedException('\TwoFAS\Account\Exception\AuthorizationException', 'Unauthorized');

        $twoFAs->createIntegration('Unauthorized integration');
    }
}