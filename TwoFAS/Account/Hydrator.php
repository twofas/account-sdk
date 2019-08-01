<?php

namespace TwoFAS\Account;

class Hydrator
{
    /**
     * @param array $responseData
     *
     * @return Client
     */
    public function getClientFromResponseData(array $responseData)
    {
        $client = new Client();
        $client
            ->setId($responseData['id'])
            ->setEmail($responseData['email'])
            ->setHasCard($responseData['has_card'])
            ->setHasGeneratedPassword($responseData['has_generated_password'])
            ->setPrimaryCardId($responseData['primary_card_id']);

        return $client;
    }

    /**
     * @param array $responseData
     *
     * @return Integration
     */
    public function getIntegrationFromResponseData(array $responseData)
    {
        $integration = new Integration();
        $integration
            ->setId($responseData['id'])
            ->setLogin($responseData['login'])
            ->setName($responseData['name'])
            ->setChannels(array(
                'sms'   => $responseData['channel_sms'],
                'call'  => $responseData['channel_call'],
                'email' => $responseData['channel_email'],
                'totp'  => $responseData['channel_totp'],
            ))
            ->setPublicKey($responseData['public_key'])
            ->setPrivateKey($responseData['private_key']);

        return $integration;
    }

    /**
     * @param array $responseData
     *
     * @return Card
     */
    public function getCardFromResponseData(array $responseData)
    {
        $card = new Card();
        $card
            ->setId($responseData['id'])
            ->setLastFour($responseData['last4'])
            ->setExpMonth($responseData['expMonth'])
            ->setExpYear($responseData['expYear']);

        return $card;
    }
}