<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2017 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Tests\Http\Factory;

use Interop\Http\Factory\RequestFactoryTestCase;
use Slim\Http\Factory\RequestFactory;
use Slim\Http\Factory\UriFactory;

class RequestFactoryTest extends RequestFactoryTestCase
{
    /**
     * @return RequestFactory
     */
    protected function createRequestFactory()
    {
        return new RequestFactory();
    }

    /**
     * @param string $uri
     * @return \Psr\Http\Message\UriInterface
     */
    protected function createUri($uri)
    {
        return (new UriFactory())->createUri($uri);
    }
}
