<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Http\Psr7Integration\Nyholm;

use Http\Psr7Test\UriIntegrationTest;
use Nyholm\Psr7\Factory\Psr17Factory;
use Slim\Http\Factory\DecoratedUriFactory;
use Slim\Tests\Http\Providers\NyholmPsr17FactoryProvider;

use function define;
use function defined;

class UriTest extends UriIntegrationTest
{
    public static function setUpBeforeClass(): void
    {
        if (!defined('STREAM_FACTORY')) {
            define('STREAM_FACTORY', Psr17Factory::class);
        }
    }

    public function createUri($uri)
    {
        $provider = new NyholmPsr17FactoryProvider();
        $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

        return $decoratedUriFactory->createUri($uri);
    }
}
