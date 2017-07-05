<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2017 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Http\Factory;

use Interop\Http\Factory\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\Http\Cookies;
use Slim\Http\Headers;
use Slim\Http\Request;

class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * Create a new server request.
     *
     * @param string $method
     * @param UriInterface|string $uri
     *
     * @return ServerRequestInterface
     */
    public function createServerRequest($method, $uri)
    {
        if (is_string($uri)) {
            $uri = (new UriFactory())->createUri($uri);
        } elseif (!$uri instanceof UriInterface) {
            throw new \InvalidArgumentException();
        }

        $body = (new StreamFactory())->createStream();

        return new Request($method, $uri, new Headers(), [], [], $body);
    }

    /**
     * Create a new server request from server variables.
     *
     * @param array $server Typically $_SERVER or similar structure.
     *
     * @return ServerRequestInterface
     *
     * @throws \InvalidArgumentException
     *  If no valid method or URI can be determined.
     */
    public function createServerRequestFromArray(array $server)
    {
        if (!isset($server['REQUEST_METHOD'])) {
            throw new \InvalidArgumentException();
        }

        $method = $server['REQUEST_METHOD'];
        $uri = (new UriFactory())->createFromGlobals($server);
        $headers = Headers::createFromGlobals($server);
        $cookies = Cookies::parseHeader($headers->get('Cookie', []));
        $serverParams = $server;
        $body = (new StreamFactory())->createStream();

        $request = new Request($method, $uri, $headers, $cookies, $serverParams, $body);

        return $request;
    }
}
