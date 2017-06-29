<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2017 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Tests\Http\Factory;

use Interop\Http\Factory\StreamFactoryTestCase;
use Slim\Http\Factory\StreamFactory;

class StreamFactoryTest extends StreamFactoryTestCase
{
    /**
     * @return StreamFactory
     */
    protected function createStreamFactory()
    {
        return new StreamFactory();
    }
}
