<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2017 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Tests\Http\Factory;

use Interop\Http\Factory\UriFactoryTestCase;
use Slim\Http\Factory\UriFactory;

class UriFactoryTest extends UriFactoryTestCase
{
    /**
     * @return UriFactory
     */
    protected function createUriFactory()
    {
        return new UriFactory();
    }
}
