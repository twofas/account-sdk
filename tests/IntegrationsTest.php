<?php

namespace TwoFAS\Account;

use TwoFAS\Account\Response\ResponseGenerator;

class IntegrationsTest extends AccountBase
{
    public function testCreating()
    {
        // setup scope
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        $name       = 'Integration name';
        $privateKey = 'MIGqAgEAAiEApxS+t3N6TzgEZPW0vk3RIJqSeeYCa5ThY8be10GzPwsCAwEAAQIgdvVROIJSkfabQlqiXmA';
        $publicKey  = 'MDwwDQYJKoZIhvcNAQEBBQADKwAwKAIhAKcUvrdzek84BGT1tL5N0SCaknnmAmuU4WPG3tdBsz8LAgMBAAE=';

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode([
                'id'          => getenv('integration_id'),
                'name'        => $name,
                'public_key'  => $publicKey,
                'private_key' => $privateKey
            ]);

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::CREATED));
        }

        $integration = $twoFAs->createIntegration($name);

        $this->assertInstanceOf('\TwoFAS\Account\Integration', $integration);
        if ($this->isDevelopmentEnvironment()) {
            $this->assertEquals(getenv('integration_id'), $integration->getId());
        }
        $this->assertNotEmpty($integration->getId());
        $this->assertEquals($name, $integration->getName());
        $this->assertNotNull($integration->getPublicKey());
        $this->assertNotNull($privateKey, $integration->getPrivateKey());
    }

    public function testGetIntegration()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        $privateKey = 'MIGqAgEAAiEApxS+t3N6TzgEZPW0vk3RIJqSeeYCa5ThY8be10GzPwsCAwEAAQIgdvVROIJSkfabQlqiXmA';
        $publicKey  = 'MDwwDQYJKoZIhvcNAQEBBQADKwAwKAIhAKcUvrdzek84BGT1tL5N0SCaknnmAmuU4WPG3tdBsz8LAgMBAAE=';

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode([
                'id'          => getenv('integration_id'),
                'name'        => 'name',
                'public_key'  => $publicKey,
                'private_key' => $privateKey
            ]);

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::OK));
        }

        $integration = $twoFAs->getIntegration(getenv('integration_id'));
        $this->assertInstanceOf('\TwoFAS\Account\Integration', $integration);
        $this->assertEquals(getenv('integration_id'), $integration->getId());
        $this->assertEquals('name', $integration->getName());
        $this->assertEquals($publicKey, $integration->getPublicKey());
        $this->assertEquals($privateKey, $integration->getPrivateKey());
    }

    public function testUpdateIntegration()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = [
                'id'   => 1,
                'name' => 'new_name',
            ];

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($response), HttpCodes::OK));
        }

        $integration = new Integration();
        $integration
            ->setId(getenv('integration_id'))
            ->setName('name');

        $integration->setName('new_name');
        $twoFAs->updateIntegration($integration);

        $this->assertEquals(getenv('integration_id'), $integration->getId());
        $this->assertEquals('new_name', $integration->getName());
    }

    public function testUpdateEncryptionKeys()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = [
                'id'          => 1,
                'name'        => 'new_name',
                'private_key' => str_repeat('a', 700),
                'public_key'  => str_repeat('b', 300)
            ];

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($response), HttpCodes::OK));
        }

        $integration = new Integration();
        $integration
            ->setId(getenv('integration_id'))
            ->setName('name');

        $this->assertNull($integration->getPublicKey());
        $this->assertNull($integration->getPrivateKey());

        $integration = $twoFAs->resetIntegrationEncryptionKeys($integration);

        $this->assertEquals(getenv('integration_id'), $integration->getId());
        $this->assertTrue(is_string($integration->getPublicKey()));
        $this->assertTrue(strlen($integration->getPublicKey()) > 200);
        $this->assertTrue(is_string($integration->getPrivateKey()));
        $this->assertTrue(strlen($integration->getPrivateKey()) > 400);
    }


    public function testCreateIntegrationThrowsValidationExceptionIfDataIsInvalid()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = $this->getExpectedValidationBody(
                [
                    'name' => ['validation.string']
                ]
            );

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($response), HttpCodes::BAD_REQUEST));
        }

        $this->setExpectedException('\TwoFAS\Account\Exception\ValidationException');

        $twoFAs->createIntegration('a');
    }

    public function testDeleteIntegration()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $integration = new Integration();

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(
                '',
                HttpCodes::NO_CONTENT
            ));
        } else {
            $integration = $twoFAs->createIntegration('hello');
        }

        $response = $twoFAs->deleteIntegration($integration);

        $this->assertInstanceOf('TwoFAS\Account\NoContent', $response);
    }

    public function testDeleteNotFoundIntegration()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        $integration = new Integration();
        $integration->setId(999999999999);

        if ($this->isDevelopmentEnvironment()) {
            $response = ['error' => [
                'code' => 10404,
                'msg'  => 'No data matching given criteria'
            ]];

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(
                json_encode($response),
                HttpCodes::NOT_FOUND
            ));
        }

        $this->setExpectedException('\TwoFAS\Account\Exception\NotFoundException');
        $twoFAs->deleteIntegration($integration);
    }
}
