<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2018 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Http\Factory;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Decorators\ServerRequestDecorator;

/**
 * Class DecoratedServerRequestFactory
 * @package Slim\Http\Factory
 */
class DecoratedServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * @var ServerRequestFactoryInterface
     */
    private $serverRequestFactory;

    /**
     * DecoratedServerRequestFactory constructor.
     * @param ServerRequestFactoryInterface $serverRequestFactory
     */
    public function __construct(ServerRequestFactoryInterface $serverRequestFactory)
    {
        $this->serverRequestFactory = $serverRequestFactory;
    }

    /**
     * @param string $method
     * @param \Psr\Http\Message\UriInterface|string $uri
     * @param array $serverParams
     * @return ServerRequestDecorator
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        $serverRequest = $this->serverRequestFactory->createServerRequest($method, $uri, $serverParams);
        return new ServerRequestDecorator($serverRequest);
    }
}
