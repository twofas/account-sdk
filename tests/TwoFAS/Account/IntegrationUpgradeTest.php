<?php

use TwoFAS\Account\HttpCodes;
use TwoFAS\Account\OAuth\Token;
use TwoFAS\Account\OAuth\TokenType;
use TwoFAS\Account\Response\ResponseGenerator;

class IntegrationUpgradeTest extends AccountBase
{
    public function testGet()
    {
        $tokenType = TokenType::wordpress();
        $token     = new Token($tokenType->getType(), getenv('oauth_second_wordpress_token'), getenv('second_integration_id'));
        $storage   = new ArrayStorage();
        $storage->storeToken($token);

        $twoFAs     = $this->getEmptyTwoFASWithCustomStorage($storage);
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode([]);

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::NO_CONTENT));
        }

        $response = $twoFAs->canIntegrationUpgrade(getenv('second_integration_id'));
        $this->assertEquals(true, $response);
    }

    public function testGetNotReady()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode([]);

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::BAD_REQUEST));
        }

        $response = $twoFAs->canIntegrationUpgrade(getenv('integration_id'));
        $this->assertEquals(false, $response);
    }

    public function testPut()
    {
        $tokenType = TokenType::wordpress();
        $token     = new Token($tokenType->getType(), getenv('oauth_second_wordpress_token'), getenv('second_integration_id'));
        $storage   = new ArrayStorage();
        $storage->storeToken($token);

        $twoFAs     = $this->getEmptyTwoFASWithCustomStorage($storage);
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode([]);

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::NO_CONTENT));
        }

        $response = $twoFAs->upgradeIntegration(getenv('second_integration_id'));
        $this->assertEquals(true, $response);
    }

    public function testPutNotReady()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode([]);

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::BAD_REQUEST));
        }

        $this->setExpectedException('\TwoFAS\Account\Exception\Exception');

        $twoFAs->upgradeIntegration(getenv('integration_id'));
    }
}
