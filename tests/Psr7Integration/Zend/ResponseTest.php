<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Http\Psr7Integration\Zend;

use Http\Psr7Test\ResponseIntegrationTest;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Tests\Http\Providers\ZendDiactorosPsr17FactoryProvider;
use Zend\Diactoros\StreamFactory;

class ResponseTest extends ResponseIntegrationTest
{
    public static function setUpBeforeClass()
    {
        if (!defined('STREAM_FACTORY')) {
            define('STREAM_FACTORY', StreamFactory::class);
        }
    }

    public function createSubject()
    {
        $provider = new ZendDiactorosPsr17FactoryProvider();
        $decoratedResponseFactory = new DecoratedResponseFactory(
            $provider->getResponseFactory(),
            $provider->getStreamFactory()
        );

        return $decoratedResponseFactory->createResponse();
    }
}
