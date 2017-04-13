<?php
/**
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim
 * @copyright Copyright (c) 2011-2015 Josh Lockhart
 * @license   https://github.com/slimphp/Slim/blob/master/LICENSE.md (MIT License)
 */

namespace Slim\Tests\Http;

use ReflectionProperty;
use Slim\Http\Collection;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\UploadedFile;
use Slim\Http\Uri;
use Slim\Http\FactoryDefault;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function requestFactory(array $customGlobals = [])
    {
        $env = Environment::mock(array_merge([
            'HTTPS' => 1,
            'HTTP_HOST' => 'example.com',
            'SERVER_PORT' => 443,
            'REQUEST_URI' => '/foo/bar?abc=123',
            'QUERY_STRING' => 'abc=123',
            'HTTP_COOKIE' => 'user=john;id=123'
        ], $customGlobals));

        return (new FactoryDefault())->makeRequest($env);
    }

    public function uriFactory(array $customGlobals = [])
    {
        $env = Environment::mock(array_merge([
            'HTTPS' => 1,
            'HTTP_HOST' => 'example.com',
            'SERVER_PORT' => 443,
            'REQUEST_URI' => '/foo/bar?abc=123',
            'QUERY_STRING' => 'abc=123',
            'HTTP_COOKIE' => 'user=john;id=123'
        ], $customGlobals));

        return (new FactoryDefault())->makeUri($env);
    }

    public function testDisableSetter()
    {
        $request = $this->requestFactory();
        $request->foo = 'bar';

        $this->assertFalse(property_exists($request, 'foo'));
    }

    public function testAddsHostHeaderFromUri()
    {
        $request = $this->requestFactory();
        $this->assertEquals('example.com', $request->getHeaderLine('Host'));
    }

    /*******************************************************************************
     * Method
     ******************************************************************************/

    public function testGetMethod()
    {
        $this->assertEquals('GET', $this->requestFactory()->getMethod());
    }

    public function testWithMethod()
    {
        $request = $this->requestFactory()->withMethod('PUT');

        $this->assertAttributeEquals('PUT', 'method', $request);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithMethodInvalid()
    {
        $this->requestFactory()->withMethod('FOO');
    }

    public function testWithMethodNull()
    {
        $request = $this->requestFactory()->withMethod(null);

        $this->assertAttributeEquals(null, 'method', $request);
    }

    public function testCreateFromEnvironmentWithMultipart()
    {
        $_POST['foo'] = 'bar';
        $request = $this->requestFactory([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/foo',
            'REQUEST_METHOD' => 'POST',
            'HTTP_CONTENT_TYPE' => 'multipart/form-data; boundary=---foo'
        ]);
        unset($_POST);

        $this->assertEquals(['foo' => 'bar'], $request->getParsedBody());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateRequestWithInvalidMethodString()
    {
        $this->requestFactory(['REQUEST_METHOD' => 'FOO']);
    }

    /*******************************************************************************
     * URI
     ******************************************************************************/

    public function testGetRequestTarget()
    {
        $this->assertEquals('/foo/bar?abc=123', $this->requestFactory()->getRequestTarget());
    }

    public function testGetRequestTargetAlreadySet()
    {
        $request = $this->requestFactory();
        $prop = new ReflectionProperty($request, 'requestTarget');
        $prop->setAccessible(true);
        $prop->setValue($request, '/foo/bar?abc=123');

        $this->assertEquals('/foo/bar?abc=123', $request->getRequestTarget());
    }

    public function testGetRequestTargetIfNoUri()
    {
        $request = $this->requestFactory();
        $prop = new ReflectionProperty($request, 'uri');
        $prop->setAccessible(true);
        $prop->setValue($request, null);

        $this->assertEquals('/', $request->getRequestTarget());
    }

    public function testWithRequestTarget()
    {
        $clone = $this->requestFactory()->withRequestTarget('/test?user=1');

        $this->assertAttributeEquals('/test?user=1', 'requestTarget', $clone);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithRequestTargetThatHasSpaces()
    {
        $this->requestFactory()->withRequestTarget('/test/m ore/stuff?user=1');
    }

    public function testGetUri()
    {
        $request = $this->requestFactory();

        $this->assertInstanceOf('\Psr\Http\Message\UriInterface', $request->getUri());
    }

    public function testWithUri()
    {
        $globals = [
            'HTTPS' => 1,
            'HTTP_HOST' => 'example2.com',
            'SERVER_PORT' => 443,
            'REQUEST_URI' => '/test?xyz=123',
            'QUERY_STRING' => 'xyz=123'
        ];
        $uriCustom = $this->uriFactory($globals);
        $request = $this->requestFactory($globals);
        $request = $request->withUri($uriCustom);

        $this->assertAttributeSame($uriCustom, 'uri', $request);
    }

    public function testGetContentType()
    {
        $request = $this->requestFactory([
            'HTTP_CONTENT_TYPE' => 'application/json;charset=utf8'
        ]);

        $this->assertEquals('application/json;charset=utf8', $request->getContentType());
    }

    public function testGetContentTypeEmpty()
    {
        $request = $this->requestFactory();

        $this->assertNull($request->getContentType());
    }

    public function testGetMediaType()
    {
        $request = $this->requestFactory([
            'HTTP_CONTENT_TYPE' => 'application/json;charset=utf8'
        ]);

        $this->assertEquals('application/json', $request->getMediaType());
    }

    public function testGetMediaTypeEmpty()
    {
        $request = $this->requestFactory();

        $this->assertNull($request->getMediaType());
    }

    /*******************************************************************************
     * Cookies
     ******************************************************************************/

    public function testGetCookieParams()
    {
        $shouldBe = [
            'user' => 'john',
            'id' => '123',
        ];

        $this->assertEquals($shouldBe, $this->requestFactory()->getCookieParams());
    }

    public function testWithCookieParams()
    {
        $request = $this->requestFactory();
        $clone = $request->withCookieParams(['type' => 'framework']);

        $this->assertEquals(['type' => 'framework'], $clone->getCookieParams());
    }

    /*******************************************************************************
     * Query Params
     ******************************************************************************/

    public function testGetQueryParams()
    {
        $this->assertEquals(['abc' => '123'], $this->requestFactory()->getQueryParams());
    }

    public function testGetQueryParamsAlreadySet()
    {
        $request = $this->requestFactory();
        $prop = new ReflectionProperty($request, 'queryParams');
        $prop->setAccessible(true);
        $prop->setValue($request, ['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $request->getQueryParams());
    }

    public function testWithQueryParams()
    {
        $request = $this->requestFactory();
        $clone = $request->withQueryParams(['foo' => 'bar']);
        $cloneUri = $clone->getUri();

        $this->assertEquals('abc=123', $cloneUri->getQuery()); // <-- Unchanged
        $this->assertAttributeEquals(['foo' => 'bar'], 'queryParams', $clone); // <-- Changed
    }

    public function testGetQueryParamsWithoutUri()
    {
        $request = $this->requestFactory();
        $prop = new ReflectionProperty($request, 'uri');
        $prop->setAccessible(true);
        $prop->setValue($request, null);

        $this->assertEquals([], $request->getQueryParams());
    }

    /*******************************************************************************
     * Uploaded files
     ******************************************************************************/

    /**
     * @covers Slim\Http\Request::withUploadedFiles
     * @covers Slim\Http\Request::getUploadedFiles
     */
    public function testWithUploadedFiles()
    {
        $files = [new UploadedFile('foo.txt'), new UploadedFile('bar.txt')];

        $request = $this->requestFactory();
        $clone = $request->withUploadedFiles($files);

        $this->assertEquals([], $request->getUploadedFiles());
        $this->assertEquals($files, $clone->getUploadedFiles());
    }

    /*******************************************************************************
     * Server Params
     ******************************************************************************/

    // TODO: Update this test

    /*public function testGetServerParams()
    {
        $mockEnv = Environment::mock();
        $request = $this->requestFactory();

        $serverParams = $request->getServerParams();
        foreach ($serverParams as $key => $value) {
            if ($key == 'REQUEST_TIME' || $key == 'REQUEST_TIME_FLOAT') {
                $this->assertGreaterThanOrEqual(
                    $mockEnv[$key],
                    $value,
                    sprintf("%s value of %s was less than expected value of %s", $key, $value, $mockEnv[$key])
                );
            } else {
                $this->assertEquals(
                    $mockEnv[$key],
                    $value,
                    sprintf("%s value of %s did not equal expected value of %s", $key, $value, $mockEnv[$key])
                );
            }
        }
    }*/

    /*******************************************************************************
     * File Params
     ******************************************************************************/

    // TODO: Write file params tests

    /*******************************************************************************
     * Attributes
     ******************************************************************************/

    public function testGetAttributes()
    {
        $request = $this->requestFactory();
        $attrProp = new ReflectionProperty($request, 'attributes');
        $attrProp->setAccessible(true);
        $attrProp->setValue($request, new Collection(['foo' => 'bar']));

        $this->assertEquals(['foo' => 'bar'], $request->getAttributes());
    }

    public function testGetAttribute()
    {
        $request = $this->requestFactory();
        $attrProp = new ReflectionProperty($request, 'attributes');
        $attrProp->setAccessible(true);
        $attrProp->setValue($request, new Collection(['foo' => 'bar']));

        $this->assertEquals('bar', $request->getAttribute('foo'));
        $this->assertNull($request->getAttribute('bar'));
        $this->assertEquals(2, $request->getAttribute('bar', 2));
    }

    public function testWithAttribute()
    {
        $request = $this->requestFactory();
        $attrProp = new ReflectionProperty($request, 'attributes');
        $attrProp->setAccessible(true);
        $attrProp->setValue($request, new Collection(['foo' => 'bar']));
        $clone = $request->withAttribute('test', '123');

        $this->assertEquals('123', $clone->getAttribute('test'));
    }

    public function testWithoutAttribute()
    {
        $request = $this->requestFactory();
        $attrProp = new ReflectionProperty($request, 'attributes');
        $attrProp->setAccessible(true);
        $attrProp->setValue($request, new Collection(['foo' => 'bar']));
        $clone = $request->withoutAttribute('foo');

        $this->assertNull($clone->getAttribute('foo'));
    }

    /*******************************************************************************
     * Body
     ******************************************************************************/

    public function testGetParsedBodyForm()
    {
        $method = 'GET';
        $uri = new Uri('https', 'example.com', 443, '/foo/bar', 'abc=123', '', '');
        $headers = new Headers();
        $headers->set('Content-Type', 'application/x-www-form-urlencoded;charset=utf8');
        $cookies = [];
        $serverParams = [];
        $body = new RequestBody();
        $body->write('foo=bar');
        $request = new Request($method, $uri, $headers, $cookies, $serverParams, $body);
        $this->assertEquals(['foo' => 'bar'], $request->getParsedBody());
    }

    public function testGetParsedBodyJson()
    {
        $method = 'GET';
        $uri = new Uri('https', 'example.com', 443, '/foo/bar', 'abc=123', '', '');
        $headers = new Headers();
        $headers->set('Content-Type', 'application/json;charset=utf8');
        $cookies = [];
        $serverParams = [];
        $body = new RequestBody();
        $body->write('{"foo":"bar"}');
        $request = new Request($method, $uri, $headers, $cookies, $serverParams, $body);

        $this->assertEquals(['foo' => 'bar'], $request->getParsedBody());
    }

    public function testGetParsedBodyXml()
    {
        $method = 'GET';
        $uri = new Uri('https', 'example.com', 443, '/foo/bar', 'abc=123', '', '');
        $headers = new Headers();
        $headers->set('Content-Type', 'application/xml;charset=utf8');
        $cookies = [];
        $serverParams = [];
        $body = new RequestBody();
        $body->write('<person><name>Josh</name></person>');
        $request = new Request($method, $uri, $headers, $cookies, $serverParams, $body);

        $this->assertEquals('Josh', $request->getParsedBody()->name);
    }

    public function testGetParsedBodyXmlWithTextXMLMediaType()
    {
        $method = 'GET';
        $uri = new Uri('https', 'example.com', 443, '/foo/bar', 'abc=123', '', '');
        $headers = new Headers();
        $headers->set('Content-Type', 'text/xml');
        $cookies = [];
        $serverParams = [];
        $body = new RequestBody();
        $body->write('<person><name>Josh</name></person>');
        $request = new Request($method, $uri, $headers, $cookies, $serverParams, $body);

        $this->assertEquals('Josh', $request->getParsedBody()->name);
    }

    public function testGetParsedBodyWhenAlreadyParsed()
    {
        $request = $this->requestFactory();
        $prop = new ReflectionProperty($request, 'bodyParsed');
        $prop->setAccessible(true);
        $prop->setValue($request, ['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $request->getParsedBody());
    }

    public function testGetParsedBodyWhenBodyDoesNotExist()
    {
        $request = $this->requestFactory();
        $prop = new ReflectionProperty($request, 'body');
        $prop->setAccessible(true);
        $prop->setValue($request, null);

        $this->assertNull($request->getParsedBody());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetParsedBodyWithParseError()
    {
        $request = $this->requestFactory([
            'HTTP_CONTENT_TYPE' => 'application/json;charset=utf8'
        ]);
        $body = $request->getBody();
        $body->write('{"foo":"bar"}');
        $body->rewind();
        $request->registerMediaTypeParser('application/json', function ($input) {
            return 10; // <-- Return invalid body value
        });
        $request->getParsedBody(); // <-- Triggers exception
    }

    public function testWithParsedBody()
    {
        $clone = $this->requestFactory()->withParsedBody(['xyz' => '123']);

        $this->assertAttributeEquals(['xyz' => '123'], 'bodyParsed', $clone);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithParsedBodyInvalid()
    {
        $this->requestFactory()->withParsedBody(2);
    }
}
