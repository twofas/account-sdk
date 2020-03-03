<?php

namespace TwoFAS\Account;

use TwoFAS\Account\Response\ResponseGenerator;

class PasswordResetTest extends AccountBase
{
    public function testResetPasswordReturnsNoContentObjectIfEmailExists()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = '';
            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::NO_CONTENT));
        }

        $noContent = $twoFAs->resetPassword(getenv('client_email'));

        $this->assertInstanceOf('\TwoFAS\Account\NoContent', $noContent);
    }

    public function testResetPasswordThrowsValidatorErrorIfValidEmailIsNotProvided()
    {
        $twoFAs     = $this->getTwoFAs();
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

        $twoFAs->resetPassword('test');
    }

    public function testResetPasswordThrowsNotFoundExceptionIfEmailIsNotFound()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = [
                'error' => [
                    'code' => 10404,
                    'msg'  => 'No data matching given criteria'
                ]
            ];

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom(json_encode($response), HttpCodes::NOT_FOUND));
        }

        $this->setExpectedException('\TwoFAS\Account\Exception\NotFoundException');

        $twoFAs->resetPassword('notfound@2fas.com');
    }
}