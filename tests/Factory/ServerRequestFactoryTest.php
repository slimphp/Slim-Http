<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2018 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Tests\Http\Factory;

use Interop\Http\Factory\ServerRequestFactoryTestCase;
use Slim\Http\Environment;
use Slim\Http\Factory\ServerRequestFactory;
use Slim\Http\Factory\UriFactory;

class ServerRequestFactoryTest extends ServerRequestFactoryTestCase
{
    /**
     * @return ServerRequestFactory
     */
    protected function createServerRequestFactory()
    {
        return new ServerRequestFactory();
    }

    /**
     * @param string $uri
     * @return \Psr\Http\Message\UriInterface
     */
    protected function createUri($uri)
    {
        return (new UriFactory())->createUri($uri);
    }

    /*******************************************************************************
     * Protocol
     ******************************************************************************/

    public function testGetProtocolVersion()
    {
        $env = Environment::mock(['SERVER_PROTOCOL' => 'HTTP/1.0']);
        $request = $this->createServerRequestFactory()->createServerRequest('GET', '', $env);

        $this->assertEquals('1.0', $request->getProtocolVersion());
    }
}
