<?php

namespace TwoFAS\Account\HttpClient;

use TwoFAS\Account\Exception\Exception;
use TwoFAS\Account\Response\Response;
use TwoFAS\Account\Response\ResponseGenerator;

class CurlClient implements ClientInterface
{
    /**
     * @param string $method
     * @param string $endpoint
     * @param array  $data
     * @param array  $headers
     *
     * @return Response
     *
     * @throws Exception
     */
    public function request($method, $endpoint, array $data = array(), array $headers = array())
    {
        $jsonInput = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->mapHeaders($headers));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonInput);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ResponseGenerator::createFrom($response, $httpCode);
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    protected function mapHeaders(array $headers)
    {
        return array_map(function($value, $key) {
            return $key . ': ' . $value;
        },
            $headers,
            array_keys($headers)
        );
    }
}
