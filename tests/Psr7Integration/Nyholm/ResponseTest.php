<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Http\Psr7Integration\Nyholm;

use Http\Psr7Test\ResponseIntegrationTest;
use Nyholm\Psr7\Factory\Psr17Factory;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Tests\Http\Providers\NyholmPsr17FactoryProvider;

use function define;
use function defined;

class ResponseTest extends ResponseIntegrationTest
{
    public static function setUpBeforeClass(): void
    {
        if (!defined('URI_FACTORY')) {
            define('URI_FACTORY', Psr17Factory::class);
        }
        if (!defined('STREAM_FACTORY')) {
            define('STREAM_FACTORY', Psr17Factory::class);
        }
        if (!defined('UPLOADED_FILE_FACTORY')) {
            define('UPLOADED_FILE_FACTORY', Psr17Factory::class);
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
