<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2018 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Tests\Http\Factory;

use Interop\Http\Factory\UploadedFileFactoryTestCase;
use Psr\Http\Message\StreamInterface;
use Slim\Http\Factory\StreamFactory;
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

    /**
     * @return StreamInterface
     */
    protected function createStream($content)
    {
        $file = tempnam(sys_get_temp_dir(), 'Slim_Http_UploadedFileTest_');
        $resource = fopen($file, 'r+');
        fwrite($resource, $content);
        rewind($resource);

        return (new StreamFactory())->createStreamFromResource($resource);
    }
}
