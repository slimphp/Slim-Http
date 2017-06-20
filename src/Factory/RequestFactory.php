<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2017 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Http\Factory;

use Interop\Http\Factory\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\Http\Headers;
use Slim\Http\Request;

class RequestFactory implements RequestFactoryInterface
{
    /**
     * Create a new request.
     *
     * @param string $method
     * @param UriInterface|string $uri
     *
     * @return RequestInterface
     */
    public function createRequest($method, $uri)
    {
        if (is_string($uri)) {
            $uri = (new UriFactory())->createUri($uri);
        } elseif (!$uri instanceof UriInterface) {
            throw new \InvalidArgumentException();
        }

        $body = (new StreamFactory())->createStream('');

        return new Request($method, $uri, new Headers(), [], [], $body);
    }
}