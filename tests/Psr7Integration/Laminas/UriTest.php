<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Http\Psr7Integration\Laminas;

use Http\Psr7Test\UriIntegrationTest;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
use Laminas\Diactoros\UriFactory;
use Slim\Http\Factory\DecoratedUriFactory;
use Slim\Tests\Http\Providers\LaminasDiactorosPsr17FactoryProvider;

use function define;
use function defined;

class UriTest extends UriIntegrationTest
{
    // https://datatracker.ietf.org/doc/html/rfc3986#section-3.3
    protected $skippedTests = [
        'testPathWithMultipleSlashes' => 'laminas-diactoros does not respect RFC3986.',
    ];

    public static function setUpBeforeClass(): void
    {
        if (!defined('URI_FACTORY')) {
            define('URI_FACTORY', UriFactory::class);
        }
        if (!defined('STREAM_FACTORY')) {
            define('STREAM_FACTORY', StreamFactory::class);
        }
        if (!defined('UPLOADED_FILE_FACTORY')) {
            define('UPLOADED_FILE_FACTORY', UploadedFileFactory::class);
        }
    }

    public function createUri($uri)
    {
        $provider = new LaminasDiactorosPsr17FactoryProvider();
        $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

        return $decoratedUriFactory->createUri($uri);
    }
}
