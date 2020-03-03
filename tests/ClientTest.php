<?php

namespace TwoFAS\Account;

use TwoFAS\Account\Response\ResponseGenerator;

class ClientTest extends AccountBase
{
    public function testGetClient()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        $clientId = getenv('client_id');
        $email    = getenv('client_email');
        $cardId   = getenv('card_id');

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode([
                'id'                     => $clientId,
                'email'                  => $email,
                'has_card'               => true,
                'has_generated_password' => true,
                'primary_card_id'        => $cardId
            ]);

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::OK));
        }

        $client = $twoFAs->getClient();

        $this->assertInstanceOf('\TwoFAS\Account\Client', $client);

        $this->assertEquals($clientId, $client->getId());
        $this->assertEquals($email, $client->getEmail());
        $this->assertTrue($client->hasCard());
        $this->assertTrue($client->hasGeneratedPassword());
        $this->assertEquals($cardId, $client->getPrimaryCardId());
    }

    public function testCreateClient()
    {
        $twoFAs     = $this->getEmptyTwoFAS();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        $email    = 'example@2fas.com';
        $password = 'simple';

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode([
                'id'                     => 1,
                'email'                  => $email,
                'has_card'               => false,
                'has_generated_password' => true,
                'primary_card_id'        => null
            ]);

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::CREATED));
        }

        $client = $twoFAs->createClient($email, $password, $password, 'wordpress');

        $this->assertInstanceOf('\TwoFAS\Account\Client', $client);
    }

    public function testCreateClientThrowsValidationExceptionIfDataIsInvalid()
    {
        $twoFAs     = $this->getEmptyTwoFAS();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = $this->getExpectedValidationBody(
                [
                    'email' => ['validation.required']
                ]
            );

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($response), HttpCodes::BAD_REQUEST));
        }

        $this->setExpectedException('\TwoFAS\Account\Exception\ValidationException');

        $twoFAs->createClient('', 'simple', 'simple', 'wordpress');
    }
}