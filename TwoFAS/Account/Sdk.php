<?php

namespace TwoFAS\Account;

use InvalidArgumentException;
use TwoFAS\Account\Exception\AuthorizationException;
use TwoFAS\Account\Exception\Exception;
use TwoFAS\Account\Exception\NotFoundException;
use TwoFAS\Account\Exception\PasswordResetAttemptsRemainingIsReachedException;
use TwoFAS\Account\Exception\TokenRefreshException;
use TwoFAS\Account\Exception\ValidationException;
use TwoFAS\Account\HttpClient\ClientInterface;
use TwoFAS\Account\HttpClient\CurlClient;
use TwoFAS\Account\OAuth\Interfaces\TokenStorage;
use TwoFAS\Account\OAuth\Token;
use TwoFAS\Account\OAuth\TokenNotFoundException;
use TwoFAS\Account\OAuth\TokenType;
use TwoFAS\Account\Response\Response;

/**
 * This is the main SDK class that is used to interact with the API.
 *
 * @package TwoFAS\Account
 */
class Sdk
{
    /**
     * @var string
     */
    const VERSION = '4.0.0';

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var string
     */
    private $baseUrl = 'https://account.2fas.com';

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var Hydrator
     */
    private $hydrator;

    /**
     * @var TokenType
     */
    private $specificIntegrationTokenType;

    /**
     * @var array
     */
    private $headers = [
        'Content-Type' => 'application/json',
        'Sdk-Version'  => self::VERSION
    ];

    /**
     * @param TokenStorage $tokenStorage
     * @param TokenType    $specificIntegration
     * @param array        $headers
     */
    public function __construct(TokenStorage $tokenStorage, TokenType $specificIntegration, array $headers = [])
    {
        $this->tokenStorage                 = $tokenStorage;
        $this->specificIntegrationTokenType = $specificIntegration;
        $this->httpClient                   = new CurlClient();
        $this->hydrator                     = new Hydrator();

        $this->addHeaders($headers);
    }

    /**
     * Set API url.
     *
     * @param string $url
     *
     * @return Sdk
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
        return $this;
    }

    /**
     * Set custom http client.
     *
     * @param ClientInterface $httpClient
     *
     * @return Sdk
     */
    public function setHttpClient(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * Used for getting client from 2FAS.
     *
     * @return Client
     *
     * @throws Exception
     */
    public function getClient()
    {
        $response = $this->call(
            $this->specificIntegrationTokenType,
            'GET',
            $this->createEndpoint('v2/me')
        );

        if ($response->matchesHttpCode(HttpCodes::OK)) {
            return $this->hydrator->getClientFromResponseData($response->getData());
        }

        throw $response->getError();
    }

    /**
     * Used for creating client in 2FAS.
     *
     * @param string $email
     * @param string $password
     * @param string $passwordConfirmation
     * @param string $source
     *
     * @return Client
     *
     * @throws Exception
     * @throws ValidationException
     */
    public function createClient($email, $password, $passwordConfirmation, $source)
    {
        $response = $this->call(
            null,
            'POST',
            $this->createEndpoint('v2/me'), [
                'email'                 => $email,
                'password'              => $password,
                'password_confirmation' => $passwordConfirmation,
                'source'                => $source
            ]
        );

        if ($response->matchesHttpCode(HttpCodes::CREATED)) {
            return $this->hydrator->getClientFromResponseData($response->getData());
        }

        throw $response->getError();
    }

    /**
     * Used for getting integration with specific id from 2FAS.
     *
     * @param int $integrationId
     *
     * @return Integration
     *
     * @throws Exception
     * @throws NotFoundException
     */
    public function getIntegration($integrationId)
    {
        $response = $this->call(
            $this->specificIntegrationTokenType,
            'GET',
            $this->createEndpoint('v2/integrations/' . $integrationId)
        );

        if ($response->matchesHttpCode(HttpCodes::OK)) {
            return $this->hydrator->getIntegrationFromResponseData($response->getData());
        }

        throw $response->getError();
    }

    /**
     * Used for creating integration in 2FAS.
     *
     * @param string $name
     *
     * @return Integration
     *
     * @throws Exception
     * @throws ValidationException
     */
    public function createIntegration($name)
    {
        $response = $this->call(
            TokenType::setup(),
            'POST',
            $this->createEndpoint('v3/integrations'), [
                'name' => $name
            ]
        );

        if ($response->matchesHttpCode(HttpCodes::CREATED)) {
            return $this->hydrator->getIntegrationFromResponseData($response->getData());
        }

        throw $response->getError();
    }

    /**
     * Used for updating integration data.
     *
     * @param Integration $integration
     *
     * @return Integration
     *
     * @throws Exception
     * @throws ValidationException
     */
    public function updateIntegration(Integration $integration)
    {
        $response = $this->call(
            $this->specificIntegrationTokenType,
            'PUT',
            $this->createEndpoint('v2/integrations/' . $integration->getId()),
            $integration->toArray()
        );

        if ($response->matchesHttpCode(HttpCodes::OK)) {
            return $integration;
        }

        throw $response->getError();
    }

    /**
     * Used for changing integrations encryption keys
     *
     * @param Integration $integration
     *
     * @return Integration
     *
     * @throws Exception
     * @throws ValidationException
     */
    public function resetIntegrationEncryptionKeys(Integration $integration)
    {
        $response = $this->call(
            $this->specificIntegrationTokenType,
            'PUT',
            $this->createEndpoint('v2/integrations/' . $integration->getId() . '/reset-encryption-keys'),
            $integration->toArray()
        );

        if ($response->matchesHttpCode(HttpCodes::OK)) {
            return $this->hydrator->getIntegrationFromResponseData($response->getData());
        }

        throw $response->getError();
    }

    /**
     * Used for checking if integration can be upgraded
     *
     * @param int $integrationId
     *
     * @return bool
     *
     * @throws Exception
     * @throws NotFoundException
     */
    public function canIntegrationUpgrade($integrationId)
    {
        $response = $this->call(
            $this->specificIntegrationTokenType,
            'GET',
            $this->createEndpoint('v2/integrations/' . $integrationId . '/upgrade')
        );

        if ($response->matchesHttpCode(HttpCodes::NO_CONTENT)) {
            return true;
        }

        if ($response->matchesHttpCode(HttpCodes::BAD_REQUEST)) {
            return false;
        }

        throw $response->getError();
    }

    /**
     * Used for upgrading integration
     *
     * @param int $integrationId
     *
     * @return bool
     *
     * @throws Exception
     * @throws NotFoundException
     */
    public function upgradeIntegration($integrationId)
    {
        $response = $this->call(
            $this->specificIntegrationTokenType,
            'PUT',
            $this->createEndpoint('v2/integrations/' . $integrationId . '/upgrade')
        );

        if ($response->matchesHttpCode(HttpCodes::NO_CONTENT)) {
            return true;
        }

        throw $response->getError();
    }

    /**
     * Used for refreshing authorisation tokens
     *
     * @param Token $token
     *
     * @return void
     *
     * @throws Exception
     * @throws TokenNotFoundException
     * @throws TokenRefreshException
     */
    public function refreshToken(Token $token)
    {
        $response = $this->call(
            TokenType::fromString($token->getType()),
            'PUT',
            $this->createEndpoint('v2/me/tokens/refresh')
        );

        if ($response->matchesHttpCode(HttpCodes::OK)) {
            $responseData = $response->getData();
            $accessToken  = $responseData['token']['accessToken'];

            $refreshedToken = new Token($token->getType(), $accessToken, $token->getIntegrationId());
            $this->tokenStorage->storeToken($refreshedToken);

            return;
        }

        throw $response->getError();
    }

    /**
     * @param Integration $integration
     *
     * @return NoContent
     *
     * @throws NotFoundException
     * @throws Exception
     */
    public function deleteIntegration(Integration $integration)
    {
        $response = $this->call(
            $this->specificIntegrationTokenType,
            'DELETE',
            $this->createEndpoint('v2/integrations/' . $integration->getId())
        );

        if ($response->matchesHttpCode(HttpCodes::NO_CONTENT)) {
            return new NoContent();
        }

        throw $response->getError();
    }

    /**
     * Used for getting primary card for specific client.
     *
     * @param Client $client
     *
     * @return Card
     *
     * @throws Exception
     * @throws NotFoundException
     */
    public function getPrimaryCard(Client $client)
    {
        if (is_null($client->getPrimaryCardId())) {
            throw new NotFoundException('No data matching given criteria');
        }

        $response = $this->call(
            $this->specificIntegrationTokenType,
            'GET',
            $this->createEndpoint("v2/billing/cards/{$client->getPrimaryCardId()}")
        );

        if ($response->matchesHttpCode(HttpCodes::OK)) {
            return $this->hydrator->getCardFromResponseData($response->getData());
        }

        throw $response->getError();
    }

    /**
     * Used for resetting password in 2fas account - it sends email with link and instructions for password reset.
     *
     * @param string $email
     *
     * @return NoContent
     *
     * @throws PasswordResetAttemptsRemainingIsReachedException
     * @throws Exception
     */
    public function resetPassword($email)
    {
        $response = $this->call(
            null,
            'POST',
            $this->createEndpoint('v2/me/password-reset'), ['email' => $email]
        );

        if ($response->matchesHttpCode(HttpCodes::NO_CONTENT)) {
            return new NoContent();
        }

        throw $response->getError();
    }

    /**
     * Used for generating OAuth Token with "Setup" scope.
     * This kind of token is used for creating Client and Integration.
     *
     * @param string $email
     * @param string $password
     *
     * @throws Exception
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function generateOAuthSetupToken($email, $password)
    {
        $response = $this->httpClient->request(
            'POST',
            $this->createEndpoint('v2/me/login'),
            [
                'email'    => $email,
                'password' => $password,
                'scope'    => TokenType::SETUP
            ],
            $this->headers
        );

        if ($response->matchesHttpCode(HttpCodes::OK)) {
            $responseData = $response->getData();
            $accessToken  = $responseData['token']['accessToken'];
            $scopes       = $responseData['token']['token']['scopes'];

            if ([TokenType::SETUP] === $scopes) {
                $token = new Token(TokenType::SETUP, $accessToken, 0);
                $this->tokenStorage->storeToken($token);

                return;
            }
        }

        throw $response->getError();
    }

    /**
     * Used for generating OAuth Token with specific scope for concrete integration.
     *
     * @param string $email
     * @param string $password
     * @param int    $integrationId
     *
     * @throws Exception
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function generateIntegrationSpecificToken($email, $password, $integrationId)
    {
        $response = $this->httpClient->request(
            'POST',
            $this->createEndpoint('v2/me/login/integration'),
            [
                'email'          => $email,
                'password'       => $password,
                'scope'          => $this->specificIntegrationTokenType->getType(),
                'integration_id' => $integrationId
            ],
            $this->headers
        );

        if ($response->matchesHttpCode(HttpCodes::OK)) {
            $responseData = $response->getData();
            $accessToken  = $responseData['token']['accessToken'];
            $scopes       = $responseData['token']['token']['scopes'];

            if ([$this->specificIntegrationTokenType->getType()] === $scopes) {
                $token = new Token($this->specificIntegrationTokenType->getType(), $accessToken, $integrationId);
                $this->tokenStorage->storeToken($token);

                return;
            }
        }

        throw $response->getError();
    }

    /**
     * Used for getting public configuration options from 2FAS.
     *
     * @return array
     *
     * @throws Exception
     */
    public function getConfig()
    {
        $response = $this->call(
            null,
            'GET',
            $this->baseUrl . '/config'
        );

        if ($response->matchesHttpCode(HttpCodes::OK)) {
            return $response->getData();
        }

        throw $response->getError();
    }

    /**
     * @param array $headers
     *
     * @throws InvalidArgumentException
     */
    private function addHeaders(array $headers)
    {
        foreach ($headers as $header => $value) {
            $key = $this->normalizeHeader($header);

            if (array_key_exists($key, $this->headers)) {
                throw new InvalidArgumentException('Existing header could not be changed: ' . $key);
            }

            $this->headers[$key] = $value;
        }
    }

    /**
     * @param string $header
     *
     * @return string
     */
    private function normalizeHeader($header)
    {
        $parts = explode('-', trim($header));

        $parts = array_map(function($part) {
            return ucfirst(strtolower($part));
        }, $parts);

        return implode('-', $parts);
    }

    /**
     * @param string $suffix
     *
     * @return string
     */
    private function createEndpoint($suffix)
    {
        return $this->baseUrl . '/' . $suffix;
    }

    /**
     * @param TokenType|null $tokenType
     * @param string         $method
     * @param string         $endpoint
     * @param array          $data
     *
     * @return Response
     *
     * @throws Exception
     * @throws TokenNotFoundException
     */
    private function call($tokenType, $method, $endpoint, array $data = [])
    {
        if (null === $tokenType) {
            $this->clearAuthorizationToken();
        } else {
            $this->setAuthorizationToken($this->tokenStorage->retrieveToken($tokenType->getType()));
        }

        return $this->httpClient->request($method, $endpoint, $data, $this->headers);
    }

    /**
     * @param Token $token
     */
    private function setAuthorizationToken($token)
    {
        $this->headers['Authorization'] = 'Bearer ' . $token->getAccessToken();
    }

    private function clearAuthorizationToken()
    {
        unset($this->headers['Authorization']);
    }
}
