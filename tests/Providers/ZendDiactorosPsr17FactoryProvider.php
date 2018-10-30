<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2018 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Tests\Http\Providers;

use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\StreamFactory;
use Zend\Diactoros\UploadedFileFactory;
use Zend\Diactoros\UriFactory;

/**
 * Class ZendDiactorosPsr17FactoryProvider
 * @package Slim\Tests\Http
 */
class ZendDiactorosPsr17FactoryProvider extends Psr17FactoryProvider
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $this->responseFactory = new ResponseFactory();
        $this->serverRequestFactory = new ServerRequestFactory();
        $this->streamFactory = new StreamFactory();
        $this->uploadedFileFactory = new UploadedFileFactory();
        $this->uriFactory = new UriFactory();
    }
}
