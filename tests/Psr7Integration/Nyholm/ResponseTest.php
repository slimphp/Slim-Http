<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2018 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Tests\Http\Psr7Integration\Nyholm;

use Http\Psr7Test\ResponseIntegrationTest;
use Nyholm\Psr7\Factory\Psr17Factory;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Tests\Http\Providers\NyholmPsr17FactoryProvider;

class ResponseTest extends ResponseIntegrationTest
{
    public static function setUpBeforeClass()
    {
        if (!defined('STREAM_FACTORY')) {
            define('STREAM_FACTORY', Psr17Factory::class);
        }
    }
    public function createSubject()
    {
        $provider = new NyholmPsr17FactoryProvider();
        $decoratedResponseFactory = new DecoratedResponseFactory(
            $provider->getResponseFactory(),
            $provider->getStreamFactory()
        );

        return $decoratedResponseFactory->createResponse();
    }
}
