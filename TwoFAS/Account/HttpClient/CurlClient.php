<?php

namespace TwoFAS\Account\HttpClient;

use TwoFAS\Account\Exception\Exception;
use TwoFAS\Account\Response\Response;
use TwoFAS\Account\Response\ResponseGenerator;

class CurlClient implements ClientInterface
{
    /**
     * @var resource
     */
    private $handle;

    public function __construct()
    {
        $this->handle = curl_init();
    }

    public function __destruct()
    {
        curl_close($this->handle);
    }

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
    public function request($method, $endpoint, array $data = [], array $headers = [])
    {
        $jsonInput = json_encode($data);

        curl_setopt($this->handle, CURLOPT_URL, $endpoint);
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $this->mapHeaders($headers));
        curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->handle, CURLOPT_POSTFIELDS, $jsonInput);
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($this->handle);
        $httpCode = curl_getinfo($this->handle, CURLINFO_HTTP_CODE);

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
