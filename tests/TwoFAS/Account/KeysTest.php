<?php

use TwoFAS\Account\HttpCodes;
use TwoFAS\Account\Response\ResponseGenerator;

class KeysTest extends AccountBase
{
    public function testCreateKeyReturnsProductionKeyIfThereIsNoError()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode(array(
                'token' => 'key_token'
            ));

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::CREATED));
        }

        $key = $twoFAs->createKey(getenv('integration_id'), 'key name');

        $this->assertInstanceOf('\TwoFAS\Account\Key', $key);
    }
}