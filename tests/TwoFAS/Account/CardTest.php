<?php

use TwoFAS\Account\Card;
use TwoFAS\Account\HttpCodes;
use TwoFAS\Account\OAuth\Token;
use TwoFAS\Account\OAuth\TokenType;
use TwoFAS\Account\Response\ResponseGenerator;

class CardTest extends AccountBase
{
    /**
     * @var Card
     */
    private $card;

    public function setUp()
    {
        parent::setUp();

        $this->card = new Card();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('TwoFAS\Account\Card', $this->card);
    }

    public function testNullAttributes()
    {
        $this->assertNull($this->card->getId());
        $this->assertNull($this->card->getLastFour());
        $this->assertNull($this->card->getExpMonth());
        $this->assertNull($this->card->getExpYear());
    }

    public function testCannotGetPrimaryCardWhenNotExists()
    {
        $this->setExpectedException('\TwoFAS\Account\Exception\NotFoundException', 'No data matching given criteria');

        $tokenType = TokenType::wordpress();
        $token     = new Token($tokenType->getType(), getenv('oauth_second_wordpress_token'), getenv('second_integration_id'));
        $storage   = new ArrayStorage();
        $storage->storeToken($token);

        $twoFAs     = $this->getEmptyTwoFASWithCustomStorage($storage);
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        if ($this->isDevelopmentEnvironment()) {
            $response = json_encode(array(
                'id'                     => getenv('second_client_id'),
                'email'                  => getenv('second_client_email'),
                'has_card'               => false,
                'has_generated_password' => true,
                'primary_card_id'        => null
            ));

            $httpClient->method('request')->willReturn(ResponseGenerator::createFrom($response, HttpCodes::OK));
        }

        $twoFAs->getPrimaryCard($twoFAs->getClient());
    }

    public function testGetPrimaryCard()
    {
        $twoFAs     = $this->getTwoFAs();
        $httpClient = $this->getHttpClient();
        $twoFAs->setHttpClient($httpClient);

        $cardId = getenv('card_id');

        if ($this->isDevelopmentEnvironment()) {
            $responseClient = json_encode(array(
                'id'                     => getenv('client_id'),
                'email'                  => getenv('client_email'),
                'has_card'               => true,
                'has_generated_password' => true,
                'primary_card_id'        => $cardId
            ));

            $responseCard = json_encode(array(
                'id'       => $cardId,
                'last4'    => '4343',
                'expMonth' => 11,
                'expYear'  => 2029
            ));

            $httpClient->expects($this->at(0))->method('request')->willReturn(ResponseGenerator::createFrom($responseClient, HttpCodes::OK));
            $httpClient->expects($this->at(1))->method('request')->willReturn(ResponseGenerator::createFrom($responseCard, HttpCodes::OK));
        }

        $card = $twoFAs->getPrimaryCard($twoFAs->getClient());

        $this->assertInstanceOf('TwoFAS\Account\Card', $card);
        $this->assertEquals($cardId, $card->getId());
        $this->assertEquals('4343', $card->getLastFour());
        $this->assertEquals(11, $card->getExpMonth());
        $this->assertEquals(2029, $card->getExpYear());
    }
}
