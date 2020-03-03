<?php

namespace TwoFAS\Account;

use TwoFAS\Account\Response\ResponseGenerator;
use TwoFAS\Account\Storage\ArrayStorage;

class CreatingKeysTest extends AccountBase
{
    public function testCreateWordpressToken()
    {
        /**
         * @var $twoFAs Sdk
         * @var $storage ArrayStorage
         */
        list($twoFAs, $storage) = $this->getEmptyTwoFASAndStorage();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode([
                'token' => [
                    'accessToken' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImY4YTdhY2FhOWI0YzhlNmZjY2RiMGIyMzMzMmRmZmQzODQxMjBlZDhkM2ZlY2Q0NTFlOWZlMmM1ZTcxZWIyYzkxZjgyMzUwMDFiOGRkYjRhIn0.eyJhdWQiOiIxIiwianRpIjoiZjhhN2FjYWE5YjRjOGU2ZmNjZGIwYjIzMzMyZGZmZDM4NDEyMGVkOGQzZmVjZDQ1MWU5ZmUyYzVlNzFlYjJjOTFmODIzNTAwMWI4ZGRiNGEiLCJpYXQiOjE0ODE4OTIyMjEsIm5iZiI6MTQ4MTg5MjIyMSwiZXhwIjo0NjM3NTY1ODIxLCJzdWIiOiIxMCIsInNjb3BlcyI6WyJ3b3JkcHJlc3MiXX0.r7N7xV8t9KIfY85D9OnM1Rlvz6wnyaTxjM2H2DCZifZhyP7K-6qds3XCczVbK9s68j8elx6mDoKLUAdGa3zeO_3D2lXZfCyifY_McPGCKiS0MLS3Ay1_FMAwEj0fNr7YBKTrIIoxrTi-zROa0u-6HpNwbzfGvK3ZidJckgApp6OKW1JZWpM-cTLex0XD5UgQYSO3zpy1mDbQO1V8WhHU8teYbMrHkXmmNPGGAc92ao2xhaCccqLhA7x9u1dSq7F2Rjrzjn8ijP5iw3fsNHugh13-70HWhpInymjxzRuIip7xDyp_W-rKV6V28naZpgY0uVknp_d_BezR8_9CSg0AiiF-ooueQd0i-rESUdcMvlR03nULk5QqLyxrMEWC3n8AwHkTcnyPkNJ3dZNFb253t3O0-Hd-R80OtRN_dohy-1AJUEJrZyKwweLvsa3akaGAFp1lMMLkHX79zsHUF41NGlU_B6ztmnjVGk7iRrGtQWysYiL3BO0UpssVyPEjWVyR8Pr4QKmbP_La7O78fZXHqMbIkBp1AR14s9qA0bko1daTqG3-2eMF1dwD84TRCJAtMPQMgO3xy0-rFk-g66R4mlyWF8VNdKq090s4axplzVMW8fLLkkt_4ZqNd05A957vKPuVpAz_n4BOCQDOHzJmiI-GzuP_ITPvF-MdfTLp5b8',
                    'token'       => [
                        'scopes'     => [
                            'wordpress'
                        ],
                        'id'         => 'f8a7acaa9b4c8e6fccdb0b23332dffd384120ed8d3fecd451e9fe2c5e71eb2c91f8235001b8ddb4a',
                        'created_at' => '2016-12-16 12:43:41',
                        'expires_at' => '2116-12-16 12:43:41',
                        'user_id'    => getenv('client_id'),
                        'updated_at' => '2016-12-16 12:43:41',
                        'revoked'    => false,
                        'client_id'  => 1,
                        'name'       => 'Wordpress'
                    ]
                ]
            ]);

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::OK));
        }

        $twoFAs->generateIntegrationSpecificToken(
            getenv('client_email'),
            getenv('client_password'),
            getenv('integration_id')
        );

        $token = $storage->retrieveToken('wordpress');

        $this->assertInstanceOf('\TwoFAS\Account\OAuth\Token', $token);
        $this->assertEquals(getenv('integration_id'), $token->getIntegrationId());
        $this->assertEquals('wordpress', $token->getType());
    }

    public function testCreateSetupToken()
    {
        /**
         * @var $twoFAs Sdk
         * @var $storage ArrayStorage
         */
        list($twoFAs, $storage) = $this->getEmptyTwoFASAndStorage();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode([
                'token' => [
                    'accessToken' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjA1MDc4YzkxYTc3M2MzZDkyYTQ0ZDMzOWEwMjNhZjA1MmNhNjk4NmM5MjZiODc0YzRkOTE3ZTE2MDEzNmY0NjM3MTYyODZlNTA0Mzk0Zjk3In0.eyJhdWQiOiIxIiwianRpIjoiMDUwNzhjOTFhNzczYzNkOTJhNDRkMzM5YTAyM2FmMDUyY2E2OTg2YzkyNmI4NzRjNGQ5MTdlMTYwMTM2ZjQ2MzcxNjI4NmU1MDQzOTRmOTciLCJpYXQiOjE0ODE4OTU0NzQsIm5iZiI6MTQ4MTg5NTQ3NCwiZXhwIjo0NjM3NTY5MDc0LCJzdWIiOiIxMCIsInNjb3BlcyI6WyJzZXR1cCJdfQ.dWFPrVWA9rrhGvQPi8ZFP7FCeGPEQtTU-kt2Xcw-gUbkfoK_sdQESded-jY5eqAu5nsqXngsYkH2drzCzoUXS_H3Cf3O-60sYBQleLTddv9tguRnRJiIUhgj1Xe0fEt2g701xkxxYffzYDxLAzktf5IlhrVxTzX-bGJNofMI7-CESMttyAx4uET8PkT4WYDesVp8Q8xyTZ29yCESlpU3EByHM_SzjqYjPtOh8CX1Xk1rA1nFAvzORH5u0688jZS2eZCntRT09eJoQJLXYWI_lBeM39i3lSj3xA0eyTojjp1VxYZ5n8NgVjlX09DS9xJuwMP904jA4EJxvF5GQedKdvP4YMzRjmbv-0jeVhqk6K5_Y1gscrq1xK2vMZbH6NhgpEPKMg9l0z18Df5OH_XbJRPrjLJuL_S1yoZV_G4t452QbA-7rbwBE9HDZrv5JtylWRjYRWoX_-LOqO1MhRtuOe4GHMYfTMj2UHpq8poo8kI5VauuJ9qtlFumHgKcyG2UbrjOmSVaO28qNeV67QpZn09Wa5ftN_j_SYiLeAJ2wUlE_qK3EBmbGRC9cbMRBVFGTeDTXP_u6AbAsNueXyi_GElb_VgspTtKWbCJQIC8nPn5oR5c0nsVXnaZA5pKtVIFOIVTfkMMMrX5tKJVlyuSqUlRYitMAXgpVLEG69kfYys',
                    'token'       => [
                        'scopes'     => [
                            'setup'
                        ],
                        'id'         => '05078c91a773c3d92a44d339a023af052ca6986c926b874c4d917e160136f463716286e504394f97',
                        'created_at' => '2016-12-16 13:37:54',
                        'expires_at' => '2116-12-16 13:37:54',
                        'user_id'    => getenv('client_id'),
                        'updated_at' => '2016-12-16 13:37:54',
                        'revoked'    => false,
                        'client_id'  => 1,
                        'name'       => 'Setup'
                    ]
                ]
            ]);

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::OK));
        }

        $twoFAs->generateOAuthSetupToken(
            getenv('client_email'),
            getenv('client_password')
        );

        $token = $storage->retrieveToken('setup');

        $this->assertInstanceOf('\TwoFAS\Account\OAuth\Token', $token);
        $this->assertEquals(0, $token->getIntegrationId());
        $this->assertEquals('setup', $token->getType());
    }

    public function testAttemptCreatingWithInvalidPassword()
    {
        $twoFAs     = $this->getEmptyTwoFAS();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode([
                'error' => [
                    'msg'  => 'Unauthorized',
                    'code' => 14007
                ]
            ]);

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::UNAUTHORIZED));
        }

        $this->setExpectedException('\TwoFAS\Account\Exception\AuthorizationException');

        $twoFAs->generateOAuthSetupToken(
            getenv('client_email'),
            'invalid_password'
        );
    }
}