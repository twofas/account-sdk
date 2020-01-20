<?php

use TwoFAS\Account\Exception\Exception as AccountException;
use TwoFAS\Account\HttpCodes;
use TwoFAS\Account\Response\ResponseGenerator;

class PublicConfigTest extends AccountBase
{
    /**
     * @throws AccountException
     */
    public function testGetConfig()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        $expectedResponse = [
            'stripe'         => ['public_key' => 'pk_test_JYoQzIG59RbN8ntc57JrGrlj'],
            'pusher'         => ['app_key' => '18d4d6b1ba877a255a74'],
            'password_reset' => ['resets_per_hour' => 3]
        ];

        if ($this->isDevelopmentEnvironment()) {
            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($expectedResponse), HttpCodes::OK));
        }

        $this->assertEquals($expectedResponse, $twoFAs->getConfig());
    }
}
