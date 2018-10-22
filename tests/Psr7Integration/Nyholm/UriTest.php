<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2018 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Tests\Http\Psr7Integration\Nyholm;

use Http\Psr7Test\UriIntegrationTest;
use Nyholm\Psr7\Factory\Psr17Factory;
use Slim\Http\Factory\DecoratedUriFactory;
use Slim\Tests\Http\Providers\NyholmPsr17FactoryProvider;
use Slim\Tests\Http\Providers\Psr17FactoryProvider;

class UriTest extends UriIntegrationTest
{
    public static function setUpBeforeClass()
    {
        if (!defined('STREAM_FACTORY')) {
            define('STREAM_FACTORY', Psr17Factory::class);
        }
    }
    public function createUri($uri)
    {
        $provider = new NyholmPsr17FactoryProvider;
        $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

        return $decoratedUriFactory->createUri($uri);
    }
}
