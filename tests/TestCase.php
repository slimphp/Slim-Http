<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Http;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Slim\Tests\Http\Providers\LaminasDiactorosPsr17FactoryProvider;
use Slim\Tests\Http\Providers\NyholmPsr17FactoryProvider;
use Slim\Tests\Http\Providers\ZendDiactorosPsr17FactoryProvider;

abstract class TestCase extends PHPUnitTestCase
{
    /**
     * @var string[]
     */
    protected $factoryProviders = [
        NyholmPsr17FactoryProvider::class,
        ZendDiactorosPsr17FactoryProvider::class,
        LaminasDiactorosPsr17FactoryProvider::class
    ];
}
