<?php
/**
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim
 * @copyright Copyright (c) 2011-2015 Josh Lockhart
 * @license   https://github.com/slimphp/Slim/blob/master/LICENSE.md (MIT License)
 */
namespace Slim\Tests\Http;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Http\FactoryDefault;

class UriTest extends TestCase
{
    public function uriFactory(array $customGlobals = [])
    {
        $env = Environment::mock(array_merge([
            'HTTPS' => '1',
            'HTTP_HOST' => 'example.com',
            'SERVER_PORT' => 443,
            'REQUEST_URI' => '/foo/bar',
            'SCRIPT_NAME' => '',
            'QUERY_STRING' => 'abc=123',
            'PHP_AUTH_USER' => 'josh',
            'PHP_AUTH_PW' => 'sekrit'
        ], $customGlobals));
        $factory = new FactoryDefault();

        return $factory->makeUri($env);
    }

    /********************************************************************************
     * Scheme
     *******************************************************************************/

    public function testGetScheme()
    {
        $this->assertEquals('https', $this->uriFactory()->getScheme());
    }

    public function testWithScheme()
    {
        $uri = $this->uriFactory()->withScheme('http');

        $this->assertAttributeEquals('http', 'scheme', $uri);
    }

    public function testWithSchemeRemovesSuffix()
    {
        $uri = $this->uriFactory()->withScheme('http://');

        $this->assertAttributeEquals('http', 'scheme', $uri);
    }

    public function testWithSchemeEmpty()
    {
        $uri = $this->uriFactory()->withScheme('');

        $this->assertAttributeEquals('', 'scheme', $uri);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Uri scheme must be one of: "", "https", "http"
     */
    public function testWithSchemeInvalid()
    {
        $this->uriFactory()->withScheme('ftp');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Uri scheme must be a string
     */
    public function testWithSchemeInvalidType()
    {
        $this->uriFactory()->withScheme([]);
    }

    /********************************************************************************
     * Authority
     *******************************************************************************/

    public function testGetAuthorityWithUsernameAndPassword()
    {
        $this->assertEquals('josh:sekrit@example.com', $this->uriFactory()->getAuthority());
    }

    public function testGetAuthorityWithUsername()
    {
        $this->assertEquals(
            'josh@example.com',
            $this->uriFactory(['PHP_AUTH_PW' => ''])->getAuthority()
        );
    }

    public function testGetAuthority()
    {
        $this->assertEquals(
            'example.com',
            $this->uriFactory([
                'PHP_AUTH_USER' => '',
                'PHP_AUTH_PW' => ''
            ])->getAuthority()
        );
    }

    public function testGetAuthorityWithNonStandardPort()
    {
        $this->assertEquals(
            'example.com:400',
            $this->uriFactory([
                'PHP_AUTH_USER' => '',
                'PHP_AUTH_PW' => '',
                'SERVER_PORT' => 400,
                'HTTPS' => 1
            ])->getAuthority()
        );
    }

    public function testGetUserInfoWithUsernameAndPassword()
    {
        $this->assertEquals('josh:sekrit', $this->uriFactory()->getUserInfo());
    }

    public function testGetUserInfoWithUsername()
    {
        $this->assertEquals('josh', $this->uriFactory([
            'PHP_AUTH_PW' => ''
        ])->getUserInfo());
    }

    public function testGetUserInfoNone()
    {
        $this->assertEquals('', $this->uriFactory([
            'PHP_AUTH_USER' => '',
            'PHP_AUTH_PW' => ''
        ])->getUserInfo());
    }

    public function testWithUserInfo()
    {
        $uri = $this->uriFactory()->withUserInfo('bob', 'pass');

        $this->assertAttributeEquals('bob', 'user', $uri);
        $this->assertAttributeEquals('pass', 'password', $uri);
    }

    public function testWithUserInfoRemovesPassword()
    {
        $uri = $this->uriFactory()->withUserInfo('bob');

        $this->assertAttributeEquals('bob', 'user', $uri);
        $this->assertAttributeEquals('', 'password', $uri);
    }

    public function testGetHost()
    {
        $this->assertEquals('example.com', $this->uriFactory()->getHost());
    }

    public function testWithHost()
    {
        $uri = $this->uriFactory()->withHost('slimframework.com');

        $this->assertAttributeEquals('slimframework.com', 'host', $uri);
    }

    public function testGetPortWithSchemeAndNonDefaultPort()
    {
        $uri = new Uri('https', 'www.example.com', 4000);

        $this->assertEquals(4000, $uri->getPort());
    }

    public function testGetPortWithSchemeAndDefaultPort()
    {
        $uriHppt = new Uri('http', 'www.example.com', 80);
        $uriHppts = new Uri('https', 'www.example.com', 443);

        $this->assertNull($uriHppt->getPort());
        $this->assertNull($uriHppts->getPort());
    }

    public function testGetPortWithoutSchemeAndPort()
    {
        $uri = new Uri('', 'www.example.com');

        $this->assertNull($uri->getPort());
    }

    public function testGetPortWithSchemeWithoutPort()
    {
        $uri = new Uri('http', 'www.example.com');

        $this->assertNull($uri->getPort());
    }

    public function testWithPort()
    {
        $uri = $this->uriFactory()->withPort(8000);

        $this->assertAttributeEquals(8000, 'port', $uri);
    }

    public function testWithPortNull()
    {
        $uri = $this->uriFactory()->withPort(null);

        $this->assertAttributeEquals(null, 'port', $uri);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWithPortInvalidInt()
    {
        $this->uriFactory()->withPort(70000);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWithPortInvalidString()
    {
        $this->uriFactory()->withPort('Foo');
    }

    /********************************************************************************
     * Path
     *******************************************************************************/

    public function testGetPath()
    {
        $this->assertEquals('/foo/bar', $this->uriFactory()->getPath());
    }

    public function testWithPath()
    {
        $uri = $this->uriFactory()->withPath('/new');

        $this->assertAttributeEquals('/new', 'path', $uri);
    }

    public function testWithPathWithoutPrefix()
    {
        $uri = $this->uriFactory()->withPath('new');

        $this->assertAttributeEquals('new', 'path', $uri);
    }

    public function testWithPathEmptyValue()
    {
        $uri = $this->uriFactory()->withPath('');

        $this->assertAttributeEquals('', 'path', $uri);
    }

    public function testWithPathUrlEncodesInput()
    {
        $uri = $this->uriFactory()->withPath('/includes?/new');

        $this->assertAttributeEquals('/includes%3F/new', 'path', $uri);
    }

    public function testWithPathDoesNotDoubleEncodeInput()
    {
        $uri = $this->uriFactory()->withPath('/include%25s/new');

        $this->assertAttributeEquals('/include%25s/new', 'path', $uri);
    }

    /**
     * @covers Slim\Http\Uri::withPath
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Uri path must be a string
     */
    public function testWithPathInvalidType()
    {
        $this->uriFactory()->withPath(['foo']);
    }

    /********************************************************************************
     * Query
     *******************************************************************************/

    public function testGetQuery()
    {
        $this->assertEquals('abc=123', $this->uriFactory()->getQuery());
    }

    public function testWithQuery()
    {
        $uri = $this->uriFactory()->withQuery('xyz=123');

        $this->assertAttributeEquals('xyz=123', 'query', $uri);
    }

    public function testWithQueryRemovesPrefix()
    {
        $uri = $this->uriFactory()->withQuery('?xyz=123');

        $this->assertAttributeEquals('xyz=123', 'query', $uri);
    }

    public function testWithQueryEmpty()
    {
        $uri = $this->uriFactory()->withQuery('');

        $this->assertAttributeEquals('', 'query', $uri);
    }

    /**
     * @covers Slim\Http\Uri::withQuery
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Uri query must be a string
     */
    public function testWithQueryInvalidType()
    {
        $this->uriFactory()->withQuery(['foo']);
    }

    /********************************************************************************
     * Fragment
     *******************************************************************************/

    public function testGetFragment()
    {
        $this->assertEquals('', $this->uriFactory()->getFragment());
    }

    public function testWithFragment()
    {
        $uri = $this->uriFactory()->withFragment('other-fragment');

        $this->assertAttributeEquals('other-fragment', 'fragment', $uri);
    }

    public function testWithFragmentRemovesPrefix()
    {
        $uri = $this->uriFactory()->withFragment('#other-fragment');

        $this->assertAttributeEquals('other-fragment', 'fragment', $uri);
    }

    public function testWithFragmentEmpty()
    {
        $uri = $this->uriFactory()->withFragment('');

        $this->assertAttributeEquals('', 'fragment', $uri);
    }

    /**
     * @covers Slim\Http\Uri::withFragment
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Uri fragment must be a string
     */
    public function testWithFragmentInvalidType()
    {
        $this->uriFactory()->withFragment(['foo']);
    }

    /********************************************************************************
     * Helpers
     *******************************************************************************/

    public function testToString()
    {
        $uri = $this->uriFactory()->withFragment('section3');

        $this->assertEquals('https://josh:sekrit@example.com/foo/bar?abc=123#section3', (string)$uri);

        $uri = $uri->withPath('bar');
        $this->assertEquals('https://josh:sekrit@example.com/bar?abc=123#section3', (string)$uri);

        $uri = $uri->withPath('/bar');
        $this->assertEquals('https://josh:sekrit@example.com/bar?abc=123#section3', (string)$uri);

        // ensure that a Uri with just a base path correctly converts to a string
        // (This occurs via createFromEnvironment when index.php is in a subdirectory)
        $uri = $this->uriFactory([
            'SCRIPT_NAME' => '/foo/index.php',
            'REQUEST_URI' => '/foo/',
            'HTTP_HOST' => 'example.com'
        ]);
        $this->assertEquals('https://josh:sekrit@example.com/foo/?abc=123', (string)$uri);
    }

    public function testCreateEnvironment()
    {
        $uri = $this->uriFactory([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/foo/bar',
            'PHP_AUTH_USER' => 'josh',
            'PHP_AUTH_PW' => 'sekrit',
            'QUERY_STRING' => 'abc=123',
            'HTTP_HOST' => 'example.com:8080',
            'SERVER_PORT' => 8080,
        ]);

        $this->assertEquals('josh:sekrit', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertEquals('8080', $uri->getPort());
        $this->assertEquals('/foo/bar', $uri->getPath());
        $this->assertEquals('abc=123', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
    }

    public function testCreateEnvironmentWithIPv6Host()
    {
        $uri = $this->uriFactory([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/foo/bar',
            'PHP_AUTH_USER' => 'josh',
            'PHP_AUTH_PW' => 'sekrit',
            'QUERY_STRING' => 'abc=123',
            'HTTP_HOST' => '[2001:db8::1]:8080',
            'REMOTE_ADDR' => '2001:db8::1',
            'SERVER_PORT' => 8080,
        ]);

        $this->assertEquals('josh:sekrit', $uri->getUserInfo());
        $this->assertEquals('[2001:db8::1]', $uri->getHost());
        $this->assertEquals('8080', $uri->getPort());
        $this->assertEquals('/foo/bar', $uri->getPath());
        $this->assertEquals('abc=123', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
    }
}
