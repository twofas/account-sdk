<?php

namespace TwoFAS\Account\Response;

use TwoFAS\Account\Exception\Exception;

/**
 * This class converts plain data to Response object.
 *
 * @package TwoFAS\Account\Response
 */
class ResponseGenerator
{
    /**
     * @param string  $body
     * @param integer $code
     *
     * @return Response
     *
     * @throws Exception
     */
    public static function createFrom($body, $code)
    {
        if ('' === $body) {
            return new Response([], $code);
        }

        $decoded = @json_decode($body, true);

        if (null === $decoded) {
            throw new Exception('Invalid response. Json expected.');
        }

        return new Response($decoded, $code);
    }
}
