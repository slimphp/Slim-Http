<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Http\Providers;

use Nyholm\Psr7\Factory\Psr17Factory;

class NyholmPsr17FactoryProvider extends Psr17FactoryProvider
{
    public function __construct()
    {
        $this->responseFactory = new Psr17Factory();
        $this->serverRequestFactory = new Psr17Factory();
        $this->streamFactory = new Psr17Factory();
        $this->uploadedFileFactory = new Psr17Factory();
        $this->uriFactory = new Psr17Factory();
    }
}
