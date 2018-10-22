<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2018 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Tests\Http\Psr7Integration\Nyholm;

use Http\Psr7Test\ServerRequestIntegrationTest;
use Nyholm\Psr7\Factory\Psr17Factory;
use Slim\Http\Factory\DecoratedServerRequestFactory;
use Slim\Tests\Http\Providers\NyholmPsr17FactoryProvider;
use Slim\Tests\Http\Providers\Psr17FactoryProvider;

class ServerRequestTest extends ServerRequestIntegrationTest
{
    public static function setUpBeforeClass()
    {
        if (!defined('STREAM_FACTORY')) {
            define('STREAM_FACTORY', Psr17Factory::class);
        }
    }
    public function createSubject()
    {
        $provider = new NyholmPsr17FactoryProvider;
        $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

        return $decoratedServerRequestFactory->createServerRequest('GET', 'http://foo.com', $_SERVER);
    }
}
