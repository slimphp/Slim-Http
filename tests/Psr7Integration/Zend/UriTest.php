<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Http\Psr7Integration\Zend;

use Http\Psr7Test\UriIntegrationTest;
use Slim\Http\Factory\DecoratedUriFactory;
use Slim\Tests\Http\Providers\ZendDiactorosPsr17FactoryProvider;
use Zend\Diactoros\StreamFactory;

class UriTest extends UriIntegrationTest
{
    public static function setUpBeforeClass(): void
    {
        if (!defined('STREAM_FACTORY')) {
            define('STREAM_FACTORY', StreamFactory::class);
        }
    }

    public function createUri($uri)
    {
        $provider = new ZendDiactorosPsr17FactoryProvider();
        $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

        return $decoratedUriFactory->createUri($uri);
    }
}
