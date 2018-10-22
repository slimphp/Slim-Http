<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2018 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Tests\Http;

use Slim\Tests\Http\Providers\NyholmPsr17FactoryProvider;
use Slim\Tests\Http\Providers\Psr17FactoryProvider;
use Slim\Tests\Http\Providers\ZendDiactorosPsr17FactoryProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class Test
 * @package Tests\SlimPsr7Decorators
 */
abstract class Test extends TestCase
{
    /**
     * @var string[]
     */
    protected $factoryProviders = [
        NyholmPsr17FactoryProvider::class,
        ZendDiactorosPsr17FactoryProvider::class,
    ];
}
