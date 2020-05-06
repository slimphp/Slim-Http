<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Http\Factory;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\Http\ServerRequest;

class DecoratedServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * @var ServerRequestFactoryInterface
     */
    protected $serverRequestFactory;

    /**
     * @param ServerRequestFactoryInterface $serverRequestFactory
     */
    public function __construct(ServerRequestFactoryInterface $serverRequestFactory)
    {
        $this->serverRequestFactory = $serverRequestFactory;
    }

    /**
     * @param string $method
     * @param UriInterface|string $uri
     * @param array{key: string, value: mixed}|array<mixed> $serverParams
     * @return ServerRequest
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        $serverRequest = $this->serverRequestFactory->createServerRequest($method, $uri, $serverParams);
        return new ServerRequest($serverRequest);
    }
}
