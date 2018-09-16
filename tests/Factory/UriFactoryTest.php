<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2018 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Tests\Http\Factory;

use Interop\Http\Factory\UriFactoryTestCase;
use Slim\Http\Environment;
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

    public function testGetAuthorityWithUsername()
    {
        $uri = $this->createUriFactory()->createUri('https://josh@example.com/foo/bar?abc=123#section3');

        $this->assertEquals('josh@example.com', $uri->getAuthority());
    }

    public function testGetAuthority()
    {
        $uri = $this->createUriFactory()->createUri('https://example.com/foo/bar?abc=123#section3');

        $this->assertEquals('example.com', $uri->getAuthority());
    }

    public function testGetAuthorityWithNonStandardPort()
    {
        $uri = $this->createUriFactory()->createUri('https://example.com:400/foo/bar?abc=123#section3');

        $this->assertEquals('example.com:400', $uri->getAuthority());
    }

    public function testGetUserInfoWithUsernameAndPassword()
    {
        $uri = $this->createUriFactory()->createUri('https://josh:sekrit@example.com:443/foo/bar?abc=123#section3');

        $this->assertEquals('josh:sekrit', $uri->getUserInfo());
    }

    public function testGetUserInfoWithUsernameAndPasswordEncodesCorrectly()
    {
        $uri = $this
            ->createUriFactory()
            ->createUri('https://bob%40example.com:pass%3Aword@example.com:443/foo/bar?abc=123#section3');

        $this->assertEquals('bob%40example.com:pass%3Aword', $uri->getUserInfo());
    }

    public function testGetUserInfoWithUsername()
    {
        $uri = $this->createUriFactory()->createUri('http://josh@example.com/foo/bar?abc=123#section3');

        $this->assertEquals('josh', $uri->getUserInfo());
    }

    public function testGetUserInfoNone()
    {
        $uri = $this->createUriFactory()->createUri('https://example.com/foo/bar?abc=123#section3');

        $this->assertEquals('', $uri->getUserInfo());
    }

    /**
     * @covers \Slim\Http\Factory\UriFactory::createUri
     */
    public function testCreateFromString()
    {
        $uri = $this->createUriFactory()->createUri('https://example.com:8080/foo/bar?abc=123');

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertEquals('8080', $uri->getPort());
        $this->assertEquals('/foo/bar', $uri->getPath());
        $this->assertEquals('abc=123', $uri->getQuery());
    }

    public function testCreateFromGlobals()
    {
        $globals = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/foo/bar',
            'PHP_AUTH_USER' => 'josh',
            'PHP_AUTH_PW' => 'sekrit',
            'QUERY_STRING' => 'abc=123',
            'HTTP_HOST' => 'example.com:8080',
            'SERVER_PORT' => 8080,
        ]);

        $uri = $this->createUriFactory()->createFromGlobals($globals);

        $this->assertEquals('josh:sekrit', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertEquals('8080', $uri->getPort());
        $this->assertEquals('/foo/bar', $uri->getPath());
        $this->assertEquals('abc=123', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
    }


    public function testCreateFromGlobalWithIPv6HostNoPort()
    {
        $environment = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/foo/bar',
            'PHP_AUTH_USER' => 'josh',
            'PHP_AUTH_PW' => 'sekrit',
            'QUERY_STRING' => 'abc=123',
            'HTTP_HOST' => '[2001:db8::1]',
            'REMOTE_ADDR' => '2001:db8::1',
            'SERVER_PORT' => 8080,
        ]);
        $uri = $this->createUriFactory()->createFromGlobals($environment);

        $this->assertEquals('josh:sekrit', $uri->getUserInfo());
        $this->assertEquals('[2001:db8::1]', $uri->getHost());
        $this->assertEquals('8080', $uri->getPort());
        $this->assertEquals('/foo/bar', $uri->getPath());
        $this->assertEquals('abc=123', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
    }

    public function testCreateFromGlobalsWithIPv6HostWithPort()
    {
        $globals = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/foo/bar',
            'PHP_AUTH_USER' => 'josh',
            'PHP_AUTH_PW' => 'sekrit',
            'QUERY_STRING' => 'abc=123',
            'HTTP_HOST' => '[2001:db8::1]:8080',
            'REMOTE_ADDR' => '2001:db8::1',
            'SERVER_PORT' => 8080,
        ]);

        $uri = $this->createUriFactory()->createFromGlobals($globals);

        $this->assertEquals('josh:sekrit', $uri->getUserInfo());
        $this->assertEquals('[2001:db8::1]', $uri->getHost());
        $this->assertEquals('8080', $uri->getPort());
        $this->assertEquals('/foo/bar', $uri->getPath());
        $this->assertEquals('abc=123', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
    }

    public function testCreateFromGlobalsWithBasePathContainingSpace()
    {
        $globals = Environment::mock([
            'SCRIPT_NAME' => "/f'oo bar/index.php",
            'REQUEST_URI' => "/f%27oo%20bar/baz",
        ]);
        $uri = $this->createUriFactory()->createFromGlobals($globals);

        $this->assertEquals('/f%27oo%20bar/baz', $uri->getPath());
    }

    public function testGetBaseUrl()
    {
        $globals = Environment::mock([
            'SCRIPT_NAME' => '/foo/index.php',
            'REQUEST_URI' => '/foo/bar',
            'QUERY_STRING' => 'abc=123',
            'HTTP_HOST' => 'example.com:80',
            'SERVER_PORT' => 80
        ]);
        $uri = $this->createUriFactory()->createFromGlobals($globals);

        $this->assertEquals('http://example.com', $uri->getBaseUrl());
    }

    public function testGetBaseUrlWithNoBasePath()
    {
        $globals = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/foo/bar',
            'QUERY_STRING' => 'abc=123',
            'HTTP_HOST' => 'example.com:80',
            'SERVER_PORT' => 80
        ]);
        $uri = $this->createUriFactory()->createFromGlobals($globals);

        $this->assertEquals('http://example.com', $uri->getBaseUrl());
    }

    public function testGetBaseUrlWithAuthority()
    {
        $globals = Environment::mock([
            'SCRIPT_NAME' => '/foo/index.php',
            'REQUEST_URI' => '/foo/bar',
            'PHP_AUTH_USER' => 'josh',
            'PHP_AUTH_PW' => 'sekrit',
            'QUERY_STRING' => 'abc=123',
            'HTTP_HOST' => 'example.com:8080',
            'SERVER_PORT' => 8080
        ]);
        $uri = $this->createUriFactory()->createFromGlobals($globals);

        $this->assertEquals('http://josh:sekrit@example.com:8080', $uri->getBaseUrl());
    }

    /**
     * @covers \Slim\Http\Factory\UriFactory::createFromGlobals
     * @ticket 1380
     */
    public function testWithPathWhenBaseRootIsEmpty()
    {
        $globals = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/bar',
        ]);
        $uri = $this->createUriFactory()->createFromGlobals($globals);

        $this->assertEquals('http://localhost/test', (string) $uri->withPath('test'));
    }

    /**
     * When the URL is /foo/index.php/bar/baz, we need the baseURL to be
     * /foo/index.php so that routing works correctly.
     *
     * @ticket 1639 as a fix to 1590 broke this.
     */
    public function testRequestURIContainsIndexDotPhp()
    {
        $uri = $this->createUriFactory()->createFromGlobals(
            Environment::mock(
                [
                    'SCRIPT_NAME' => '/foo/index.php',
                    'REQUEST_URI' => '/foo/index.php/bar/baz',
                ]
            )
        );
        $this->assertSame('/foo/index.php/bar/baz', $uri->getPath());
    }

    public function testRequestURICanContainParams()
    {
        $uri = $this->createUriFactory()->createFromGlobals(
            Environment::mock(
                [
                    'REQUEST_URI' => '/foo?abc=123',
                ]
            )
        );
        $this->assertEquals('abc=123', $uri->getQuery());
    }

    public function testUriDistinguishZeroFromEmptyString()
    {
        $expected = 'https://0:0@0:1/0?0#0';
        $this->assertSame($expected, (string)$this->createUriFactory()->createUri($expected));
    }

    public function testGetBaseUrlDistinguishZeroFromEmptyString()
    {
        $expected = 'https://0:0@0:1/0?0#0';
        $this->assertSame('https://0:0@0:1', (string)$this->createUriFactory()->createUri($expected)->getBaseUrl());
    }
}
