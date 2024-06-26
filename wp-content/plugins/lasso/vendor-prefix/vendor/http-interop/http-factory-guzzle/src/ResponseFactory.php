<?php

namespace LassoVendor\Http\Factory\Guzzle;

use LassoVendor\GuzzleHttp\Psr7\Response;
use LassoVendor\Psr\Http\Message\ResponseFactoryInterface;
use LassoVendor\Psr\Http\Message\ResponseInterface;
class ResponseFactory implements ResponseFactoryInterface
{
    public function createResponse(int $code = 200, string $reasonPhrase = '') : ResponseInterface
    {
        return new Response($code, [], null, '1.1', $reasonPhrase);
    }
}
