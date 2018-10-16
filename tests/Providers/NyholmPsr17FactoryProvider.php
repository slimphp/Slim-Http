<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2018 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Tests\Http\Providers;

use Nyholm\Psr7\Factory\Psr17Factory;

/**
 * Class NyholmPsr17FactoryProvider
 * @package Slim\Tests\Http
 */
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
