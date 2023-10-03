<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Http\Psr7Integration\Laminas;

use Http\Psr7Test\ServerRequestIntegrationTest;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
use Laminas\Diactoros\UriFactory;
use Slim\Http\Factory\DecoratedServerRequestFactory;
use Slim\Tests\Http\Providers\LaminasDiactorosPsr17FactoryProvider;

use function define;
use function defined;

class ServerRequestTest extends ServerRequestIntegrationTest
{
    protected $skippedTests = [
        'testUriPreserveHost_NoHost_Host' => 'laminas-diactoros is not compliant.'
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

    public function createSubject()
    {
        $provider = new LaminasDiactorosPsr17FactoryProvider();
        $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

        return $decoratedServerRequestFactory->createServerRequest('GET', 'http://foo.com', $_SERVER);
    }
}
