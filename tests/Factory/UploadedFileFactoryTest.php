<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2017 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Tests\Http\Factory;

use Interop\Http\Factory\UploadedFileFactoryTestCase;
use Slim\Http\Factory\UploadedFileFactory;

class UploadedFileFactoryTest extends UploadedFileFactoryTestCase
{
    /**
     * @return UploadedFileFactory
     */
    protected function createUploadedFileFactory()
    {
        return new UploadedFileFactory();
    }
}
