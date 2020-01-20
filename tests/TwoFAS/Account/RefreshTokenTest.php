<?php

use TwoFAS\Account\Exception\Exception as AccountException;
use TwoFAS\Account\HttpCodes;
use TwoFAS\Account\OAuth\Token;
use TwoFAS\Account\OAuth\TokenType;
use TwoFAS\Account\Response\ResponseGenerator;

class RefreshTokenTest extends AccountBase
{
    /**
     * @throws AccountException
     */
    public function testCannotRefreshNewToken()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        $storage = new FilledStorage();

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode([
                'error' => [
                    'code' => 10100,
                    'msg'  => 'Token should not be refreshed'
                ]
            ]);

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::BAD_REQUEST));
        }

        $this->setExpectedException('\TwoFAS\Account\Exception\TokenRefreshException', 'Token should not be refreshed');

        $twoFAs->refreshToken($storage->retrieveToken(TokenType::WORDPRESS));
    }

    /**
     * @throws AccountException
     */
    public function testRefreshing()
    {
        $tokenType = TokenType::wordpress();
        $token     = new Token($tokenType->getType(), getenv('oauth_expiring_wordpress_token'), getenv('integration_id'));
        $storage   = new ArrayStorage();
        $storage->storeToken($token);

        $firstAT = $storage->retrieveToken($tokenType->getType())->getAccessToken();

        $twoFAs     = $this->getEmptyTwoFASWithCustomStorage($storage);
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode([
                'token' => [
                    'accessToken' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjA1MDc4YzkxYTc3M2MzZDkyYTQ0ZDMzOWEwMjNhZjA1MmNhNjk4NmM5MjZiODc0YzRkOTE3ZTE2MDEzNmY0NjM3MTYyODZlNTA0Mzk0Zjk3In0.eyJhdWQiOiIxIiwianRpIjoiMDUwNzhjOTFhNzczYzNkOTJhNDRkMzM5YTAyM2FmMDUyY2E2OTg2YzkyNmI4NzRjNGQ5MTdlMTYwMTM2ZjQ2MzcxNjI4NmU1MDQzOTRmOTciLCJpYXQiOjE0ODE4OTU0NzQsIm5iZiI6MTQ4MTg5NTQ3NCwiZXhwIjo0NjM3NTY5MDc0LCJzdWIiOiIxMCIsInNjb3BlcyI6WyJzZXR1cCJdfQ.dWFPrVWA9rrhGvQPi8ZFP7FCeGPEQtTU-kt2Xcw-gUbkfoK_sdQESded-jY5eqAu5nsqXngsYkH2drzCzoUXS_H3Cf3O-60sYBQleLTddv9tguRnRJiIUhgj1Xe0fEt2g701xkxxYffzYDxLAzktf5IlhrVxTzX-bGJNofMI7-CESMttyAx4uET8PkT4WYDesVp8Q8xyTZ29yCESlpU3EByHM_SzjqYjPtOh8CX1Xk1rA1nFAvzORH5u0688jZS2eZCntRT09eJoQJLXYWI_lBeM39i3lSj3xA0eyTojjp1VxYZ5n8NgVjlX09DS9xJuwMP904jA4EJxvF5GQedKdvP4YMzRjmbv-0jeVhqk6K5_Y1gscrq1xK2vMZbH6NhgpEPKMg9l0z18Df5OH_XbJRPrjLJuL_S1yoZV_G4t452QbA-7rbwBE9HDZrv5JtylWRjYRWoX_-LOqO1MhRtuOe4GHMYfTMj2UHpq8poo8kI5VauuJ9qtlFumHgKcyG2UbrjOmSVaO28qNeV67QpZn09Wa5ftN_j_SYiLeAJ2wUlE_qK3EBmbGRC9cbMRBVFGTeDTXP_u6AbAsNueXyi_GElb_VgspTtKWbCJQIC8nPn5oR5c0nsVXnaZA5pKtVIFOIVTfkMMMrX5tKJVlyuSqUlRYitMAXgpVLEG69kfYys',
                    'token'       => [
                        'scopes'     => [
                            'wordpress'
                        ],
                        'id'         => '05078c91a773c3d92a44d339a023af052ca6986c926b874c4d917e160136f463716286e504394f97',
                        'created_at' => '2016-12-16 13:37:54',
                        'expires_at' => '2116-12-16 13:37:54',
                        'user_id'    => getenv('client_id'),
                        'updated_at' => '2016-12-16 13:37:54',
                        'revoked'    => false,
                        'client_id'  => 1,
                        'name'       => 'Wordpress'
                    ]
                ]
            ]);

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::OK));
        }

        $twoFAs->refreshToken($storage->retrieveToken(TokenType::WORDPRESS));

        $secondAT = $storage->retrieveToken($tokenType->getType())->getAccessToken();

        $this->assertNotEquals($firstAT, $secondAT);
    }
}