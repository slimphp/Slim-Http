<?php
namespace Slim\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class FactoryDefault implements FactoryInterface
{
    /**
     * Make request
     *
     * @param  array $globals The $_SERVER super-global
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function makeRequest(array $globals) : ServerRequestInterface
    {
        $method = $globals['REQUEST_METHOD'];
        $uri = $this->makeUri($globals);
        $headers = $this->makeHeaders($globals);
        $cookies = Cookies::parseHeader($headers->get('Cookie', []));
        $body = $this->makeBody();
        $files = []; // TODO: Create factory method for uploaded files
        $request = new Request($method, $uri, $headers, $cookies, $globals, $body, $files);
        if ($method === 'POST' && in_array($request->getMediaType(), ['application/x-www-form-urlencoded', 'multipart/form-data'])
        ) {
            // parsed body must be $_POST
            $request = $request->withParsedBody($_POST);
        }

        return $request;
    }

    /**
     * Make uri
     *
     * @param  array $globals The $_SERVER super-global
     * @return \Psr\Http\Message\UriInterface
     */
    public function makeUri(array $globals) : UriInterface
    {
        $env = new Collection($globals);

        // Scheme
        $isSecure = $env->get('HTTPS');
        $scheme = (empty($isSecure) || $isSecure === 'off') ? 'http' : 'https';

        // Authority: Username and password
        $username = $env->get('PHP_AUTH_USER', '');
        $password = $env->get('PHP_AUTH_PW', '');

        // Authority: Host
        if ($env->has('HTTP_HOST')) {
            $host = $env->get('HTTP_HOST');
        } else {
            $host = $env->get('SERVER_NAME');
        }

        // Authority: Port
        $port = (int)$env->get('SERVER_PORT', 80);
        if (preg_match('/^(\[[a-fA-F0-9:.]+\])(:\d+)?\z/', $host, $matches)) {
            $host = $matches[1];

            if ($matches[2]) {
                $port = (int) substr($matches[2], 1);
            }
        } else {
            $pos = strpos($host, ':');
            if ($pos !== false) {
                $port = (int) substr($host, $pos + 1);
                $host = strstr($host, ':', true);
            }
        }

        // parse_url() requires a full URL. As we don't extract the domain name or scheme,
        // we use a stand-in.
        $requestUri = parse_url('http://example.com' . $env->get('REQUEST_URI'), PHP_URL_PATH);

        // Query string
        $queryString = $env->get('QUERY_STRING', '');

        // Fragment
        $fragment = '';

        // Build Uri
        return new Uri($scheme, $host, $port, $requestUri, $queryString, $fragment, $username, $password);
    }

    /**
     * Make headers
     *
     * @param  array $globals The $_SERVER super-global
     * @return \Slim\Http\HeadersInterface
     */
    public function makeHeaders(array $globals) : HeadersInterface
    {
        $special = [
            'CONTENT_TYPE' => 1,
            'CONTENT_LENGTH' => 1,
            'PHP_AUTH_USER' => 1,
            'PHP_AUTH_PW' => 1,
            'PHP_AUTH_DIGEST' => 1,
            'AUTH_TYPE' => 1,
        ];
        $data = [];
        foreach ($globals as $key => $value) {
            $key = strtoupper($key);
            if (isset($special[$key]) || strpos($key, 'HTTP_') === 0) {
                if ($key !== 'HTTP_CONTENT_LENGTH') {
                    $data[$key] =  $value;
                }
            }
        }

        return new Headers($data);
    }

    /**
     * Make body
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function makeBody() : StreamInterface
    {
        $stream = fopen('php://temp', 'w+');
        stream_copy_to_stream(fopen('php://input', 'r'), $stream);
        rewind($stream);

        return new Stream($stream);
    }
}
