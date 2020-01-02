<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Http\Providers;

use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
use Laminas\Diactoros\UriFactory;

class LaminasDiactorosPsr17FactoryProvider extends Psr17FactoryProvider
{
    public function __construct()
    {
        $this->responseFactory = new ResponseFactory();
        $this->serverRequestFactory = new ServerRequestFactory();
        $this->streamFactory = new StreamFactory();
        $this->uploadedFileFactory = new UploadedFileFactory();
        $this->uriFactory = new UriFactory();
    }
}
