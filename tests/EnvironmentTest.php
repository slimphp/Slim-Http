<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim
 * @copyright Copyright (c) 2011-2017 Josh Lockhart
 * @license   https://github.com/slimphp/Slim/blob/3.x/LICENSE.md (MIT License)
 */
namespace Slim\Tests\Http;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;

class EnvironmentTest extends TestCase
{
    /**
     * Test environment from mock data
     */
    public function testMock()
    {
        $env = Environment::mock([
            'SCRIPT_NAME' => '/foo/bar/index.php',
            'REQUEST_URI' => '/foo/bar?abc=123',
        ]);

        $this->assertEquals('/foo/bar/index.php', $env['SCRIPT_NAME']);
        $this->assertEquals('/foo/bar?abc=123', $env['REQUEST_URI']);
        $this->assertEquals('localhost', $env['HTTP_HOST']);
    }
}
