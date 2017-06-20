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
use Slim\Http\Uri;

class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * Special HTTP headers that do not have the "HTTP_" prefix
     *
     * @var array
     */
    protected static $specialHeaders = [
        'CONTENT_TYPE' => 1,
        'CONTENT_LENGTH' => 1,
        'PHP_AUTH_USER' => 1,
        'PHP_AUTH_PW' => 1,
        'PHP_AUTH_DIGEST' => 1,
        'AUTH_TYPE' => 1,
    ];

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

        $body = (new StreamFactory())->createStream('');

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
        // TODO look into Uri::createFromGlobals() for better uri factoring
        $uri = new Uri(
            $server['REQUEST_SCHEME'],
            $server['SERVER_NAME'],
            $server['SERVER_PORT'],
            $server['REQUEST_URI'],
            $server['QUERY_STRING'],
            '',
            '',
            ''
        );

        $body = (new StreamFactory())->createStream('');

        $data = [];
        $authorization = isset($server['HTTP_AUTHORIZATION']) ? $server['HTTP_AUTHORIZATION'] : null;

        if (empty($authorization) && is_callable('getallheaders')) {
            $allHeaders = getallheaders();
            $allHeaders = array_change_key_case($allHeaders, CASE_LOWER);
            if (isset($allHeaders['authorization'])) {
                $server['HTTP_AUTHORIZATION'] = $allHeaders['authorization'];
            }
        }

        foreach ($server as $key => $value) {
            $key = strtoupper($key);
            if (isset(static::$specialHeaders[$key]) || strpos($key, 'HTTP_') === 0) {
                if ($key !== 'HTTP_CONTENT_LENGTH') {
                    $data[$key] =  $value;
                }
            }
        }

        $headers = new Headers($data);
        $cookies = Cookies::parseHeader($headers->get('Cookie', []));

        return new Request($server['REQUEST_METHOD'], $uri, $headers, $cookies, $server, $body);
    }
}