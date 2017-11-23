<?php
/**
 * @author Klyachin Andrew <sfdiem5@gmail.com>
 */

namespace Orfogrammka\Support\Http;

use Psr\Http\Message\ResponseInterface;

class Response
{
    public static function isSuccessful(ResponseInterface $response)
    {
        $statusCode = $response->getStatusCode();

        return ($statusCode >= 200 && $statusCode < 300) || $statusCode == 304;
    }

    public static function isUnauthorized(ResponseInterface $response)
    {
        $statusCode = $response->getStatusCode();

        return $statusCode == 401;
    }
}