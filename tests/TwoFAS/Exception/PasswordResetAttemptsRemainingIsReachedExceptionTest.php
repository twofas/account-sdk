<?php

use TwoFAS\Account\Exception\PasswordResetAttemptsRemainingIsReachedException;
use TwoFAS\Account\HttpClient\CurlClient;
use TwoFAS\Account\HttpCodes;
use TwoFAS\Account\Response\ResponseGenerator;

class PasswordResetAttemptsRemainingIsReachedExceptionTest extends AccountBase
{
    public function testException()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        $minutesToNextReset = 12;

        $response = json_encode([
            'error' => [
                'code'    => 14403,
                'msg'     => 'Limit of password reset attempts is already reached',
                'payload' => [
                    'minutes_to_next_reset' => $minutesToNextReset
                ]
            ]
        ]);

        $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::FORBIDDEN));

        try {
            $twoFAs->resetPassword('foo@bar.com');

            $this->fail('PasswordResetAttemptsRemainingIsReachedException not be thrown');

        } catch (PasswordResetAttemptsRemainingIsReachedException $exception) {
            $this->assertEquals(14403, $exception->getCode());
            $this->assertEquals('Limit of password reset attempts is already reached', $exception->getMessage());
            $this->assertEquals($minutesToNextReset, $exception->getMinutesToNextReset());
        }
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|CurlClient
     */
    protected function getHttpClient()
    {
        return $this->getMockBuilder('\TwoFAS\Account\HttpClient\CurlClient')->getMock();
    }
}
