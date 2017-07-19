<?php

use TwoFAS\Account\HttpCodes;
use TwoFAS\Account\Response\ResponseGenerator;

class PublicConfigTest extends AccountBase
{
    public function testGetConfig()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        $expectedResponse = array(
            'stripe'         => array('public_key' => 'pk_test_pS4JkXi9I3r0ToAieFhg7u7m'),
            'pusher'         => array('app_key' => '18d4d6b1ba877a255a74'),
            'password_reset' => array('resets_per_hour' => 3)
        );

        if ($this->isDevelopmentEnvironment()) {
            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($expectedResponse), HttpCodes::OK));
        }

        $this->assertEquals($expectedResponse, $twoFAs->getConfig());
    }
}
