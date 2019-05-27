<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Http;

use InvalidArgumentException;
use RuntimeException;
use Slim\Http\Factory\DecoratedServerRequestFactory;
use Slim\Tests\Http\Providers\Psr17FactoryProvider;

class ServerRequestTest extends TestCase
{
    public function testDisableSetter()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', '/');
            $request->foo = 'bar';

            $this->assertFalse(property_exists($request, 'foo'));
        }
    }

    public function testAddsHostHeaderFromUri()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'http://example.com');
            $this->assertEquals('example.com', $request->getHeaderLine('Host'));
        }
    }

    public function testGetMethod()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', '/');
            $this->assertEquals('GET', $request->getMethod());
        }
    }

    public function testWithMethod()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', '/');
            $request = $request->withMethod('POST');
            $this->assertEquals('POST', $request->getMethod());
        }
    }

    public function testWithMethodCaseSensitive()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', '/');
            $request = $request->withMethod('pOsT');
            $this->assertEquals('pOsT', $request->getMethod());
        }
    }

    public function testWithAllAllowedCharactersMethod()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', '/');
            $request = $request->withMethod("!#$%&'*+.^_`|~09AZ-");
            $this->assertEquals("!#$%&'*+.^_`|~09AZ-", $request->getMethod());
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWithMethodInvalid()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', '/');
            $request->withMethod('B@R');
        }
    }

    public function testIsGet()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', '/');
            $this->assertEquals(true, $request->isGet());
        }
    }

    public function testIsPost()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('POST', '/');
            $this->assertEquals(true, $request->isPost());
        }
    }

    public function testIsPut()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('PUT', '/');
            $this->assertEquals(true, $request->isPut());
        }
    }

    public function testIsPatch()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('PATCH', '/');
            $this->assertEquals(true, $request->isPatch());
        }
    }

    public function testIsDelete()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('DELETE', '/');
            $this->assertEquals(true, $request->isDelete());
        }
    }

    public function testIsHead()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('HEAD', '/');
            $this->assertEquals(true, $request->isHead());
        }
    }

    public function testIsOptions()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('OPTIONS', '/');
            $this->assertEquals(true, $request->isOptions());
        }
    }

    public function testIsXhr()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', '/');
            $request = $request
                ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
                ->withHeader('X-Requested-With', 'XMLHttpRequest');

            $this->assertEquals(true, $request->isXhr());
        }
    }

    public function testGetRequestTarget()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com/foo/bar?abc=123');
            $this->assertEquals('/foo/bar?abc=123', $request->getRequestTarget());
        }
    }

    public function testWithRequestTarget()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com/foo/bar?abc=123');
            $request = $request->withRequestTarget('/foo/bar?abc=123');

            $this->assertEquals('/foo/bar?abc=123', $request->getRequestTarget());
        }
    }

    public function testGetUri()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $this->assertEquals('https://google.com', $request->getUri());
        }
    }

    public function testWithUri()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $uriFactory = $provider->getUriFactory();
            $uri = $uriFactory->createUri('https://example.com');

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withUri($uri);

            $this->assertEquals('https://example.com', $request->getUri());
        }
    }

    public function testGetContentType()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withHeader('Content-Type', 'application/json');

            $this->assertEquals('application/json', $request->getContentType());
        }
    }

    public function testGetContentTypeEmpty()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $this->assertNull($request->getContentType());
        }
    }

    public function testGetMediaType()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withHeader('Content-Type', 'application/json');

            $this->assertEquals('application/json', $request->getMediaType());
        }
    }

    public function testGetMediaTypeEmpty()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $this->assertNull($request->getMediaType());
        }
    }

    public function testGetMediaTypeParams()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withHeader('Content-Type', 'application/json;charset=utf8;foo=bar');

            $this->assertEquals(['charset' => 'utf8', 'foo' => 'bar'], $request->getMediaTypeParams());
        }
    }

    public function testGetMediaTypeParamsEmpty()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withHeader('Content-Type', 'application/json');

            $this->assertEquals([], $request->getMediaTypeParams());
        }
    }

    public function testGetMediaTypeParamsWithoutHeader()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $this->assertEquals([], $request->getMediaTypeParams());
        }
    }

    public function testGetContentCharset()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withHeader('Content-Type', 'application/json;charset=utf8');

            $this->assertEquals('utf8', $request->getContentCharset());
        }
    }

    public function testGetContentCharsetEmpty()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withHeader('Content-Type', 'application/json');

            $this->assertNull($request->getContentCharset());
        }
    }

    public function testGetContentCharsetWithoutHeader()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $this->assertNull($request->getContentCharset());
        }
    }

    public function testGetContentLength()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withHeader('Content-Length', '150');

            $this->assertEquals(150, $request->getContentLength());
        }
    }

    public function testGetContentLengthWithoutHeader()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');

            $this->assertNull($request->getContentLength());
        }
    }

    public function testGetCookieParam()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withCookieParams(['user' => 'john']);

            $this->assertEquals('john', $request->getCookieParam('user'));
        }
    }

    public function testGetCookieParamWithDefault()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $this->assertEquals('john', $request->getCookieParam('user', 'john'));
        }
    }

    public function testGetCookieParams()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withCookieParams(['user' => 'john', 'password' => '123']);

            $this->assertEquals(['user' => 'john', 'password' => '123'], $request->getCookieParams());
        }
    }

    public function testWithCookieParams()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withCookieParams(['user' => 'john']);

            $this->assertEquals(['user' => 'john'], $request->getCookieParams());
        }
    }

    public function testGetQueryParams()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withQueryParams(['bar' => '123']);

            $this->assertEquals(['bar' => '123'], $request->getQueryParams());
        }
    }

    public function testWithQueryParams()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withQueryParams(['bar' => '123']);

            $this->assertEquals(['bar' => '123'], $request->getQueryParams());
        }
    }

    public function testWithQueryParamsEmpty()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withQueryParams(['bar' => '123']);
            $clone = $request->withQueryParams([]);

            $this->assertEquals(['bar' => '123'], $request->getQueryParams());
            $this->assertEquals([], $clone->getQueryParams());
        }
    }

    public function testWithUploadedFiles()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $streamFactory = $provider->getStreamFactory();
            $stream = $streamFactory->createStream('test');

            $uploadedFileFactory = $provider->getUploadedFileFactory();
            $files = [$uploadedFileFactory->createUploadedFile($stream, null, 0, 'foo.txt')];

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $clone = $request->withUploadedFiles($files);

            $this->assertEquals([], $request->getUploadedFiles());
            $this->assertEquals($files, $clone->getUploadedFiles());
        }
    }

    public function testGetServerParam()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $serverParams = ['HTTP_AUTHORIZATION' => 'test'];
            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com', $serverParams);

            $this->assertEquals('test', $request->getServerParam('HTTP_AUTHORIZATION'));
        }
    }

    public function testGetServerParamWithDefault()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $this->assertEquals('test', $request->getServerParam('HTTP_AUTHORIZATION', 'test'));
        }
    }

    public function testGetServerParams()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $serverParams = ['HTTP_AUTHORIZATION' => 'test'];
            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com', $serverParams);

            $this->assertEquals($serverParams, $request->getServerParams());
        }
    }

    public function testGetAttribute()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withAttribute('foo', 'bar');

            $this->assertEquals('bar', $request->getAttribute('foo'));
        }
    }

    public function testGetAttributes()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request
                ->withAttribute('foo', 'bar')
                ->withAttribute('bar', 'baz');

            $this->assertEquals(['foo' => 'bar', 'bar' => 'baz'], $request->getAttributes());
        }
    }

    public function testWithAttribute()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withAttribute('foo', 'bar');
            $clone = $request->withAttribute('foo', 'baz');

            $this->assertEquals('baz', $clone->getAttribute('foo'));
        }
    }

    public function testWithAttributes()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withAttributes(['foo' => 'bar', 'bar' => 'baz']);

            $this->assertEquals(['foo' => 'bar', 'bar' => 'baz'], $request->getAttributes());
        }
    }

    public function testWithoutAttribute()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withAttributes(['foo' => 'bar', 'bar' => 'baz']);
            $request = $request->withoutAttribute('bar');

            $this->assertEquals(['foo' => 'bar'], $request->getAttributes());
        }
    }

    public function testGetParsedBody()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request->withParsedBody(['foo' => 'bar']);

            $this->assertEquals(['foo' => 'bar'], $request->getParsedBody());
        }
    }

    public function testGetParsedBodyNull()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request->withParsedBody([]);
            $clone = $request->withParsedBody(null);

            $this->assertNull($clone->getParsedBody());
        }
    }

    public function testGetParsedBodyForm()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $streamFactory = $provider->getStreamFactory();
            $stream = $streamFactory->createStream('foo=bar');

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request
                ->withHeader('Content-Type', 'application/x-www-form-urlencoded;charset=utf8')
                ->withBody($stream);

            $this->assertEquals(['foo' => 'bar'], $request->getParsedBody());
        }
    }

    public function testGetParsedBodyJson()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $streamFactory = $provider->getStreamFactory();
            $stream = $streamFactory->createStream('{"foo":"bar"}');

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request
                ->withHeader('Content-Type', 'application/json;charset=utf8')
                ->withBody($stream);

            $this->assertEquals(['foo' => 'bar'], $request->getParsedBody());
        }
    }

    public function testGetParsedBodyInvalidJson()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $streamFactory = $provider->getStreamFactory();
            $stream = $streamFactory->createStream('{"foo"}/bar');

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request
                ->withHeader('Content-Type', 'application/json;charset=utf8')
                ->withBody($stream);

            $this->assertNull($request->getParsedBody());
        }
    }

    public function testGetParsedBodySemiValidJson()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $streamFactory = $provider->getStreamFactory();
            $stream = $streamFactory->createStream('"foo bar"');

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request
                ->withHeader('Content-Type', 'application/json;charset=utf8')
                ->withBody($stream);

            $this->assertNull($request->getParsedBody());
        }
    }

    public function testGetParsedBodyWithJsonStructuredSuffix()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $streamFactory = $provider->getStreamFactory();
            $stream = $streamFactory->createStream('{"foo":"bar"}');

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request
                ->withHeader('Content-Type', 'application/vnd.api+json;charset=utf8')
                ->withBody($stream);

            $this->assertEquals(['foo' => 'bar'], $request->getParsedBody());
        }
    }

    public function testGetParsedBodyWithJsonStructuredSuffixAndRegisteredParser()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $streamFactory = $provider->getStreamFactory();
            $stream = $streamFactory->createStream('{"foo":"bar"}');

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request
                ->withHeader('Content-Type', 'application/vnd.api+json;charset=utf8')
                ->withBody($stream);

            $request->registerMediaTypeParser('application/vnd.api+json', function ($input) {
                return ['data' => $input];
            });

            $this->assertEquals(['data' => '{"foo":"bar"}'], $request->getParsedBody());
        }
    }

    public function testGetParsedBodyXml()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $streamFactory = $provider->getStreamFactory();
            $stream = $streamFactory->createStream('<person><name>John</name></person>');

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request
                ->withHeader('Content-Type', 'application/hal+xml;charset=utf8')
                ->withBody($stream);

            /** @var \stdClass $obj */
            $obj = $request->getParsedBody();
            $this->assertEquals('John', $obj->name);
        }
    }

    public function testGetParsedBodyStructuredSuffixXml()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $streamFactory = $provider->getStreamFactory();
            $stream = $streamFactory->createStream('<person><name>John</name></person>');

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request
                ->withHeader('Content-Type', 'application/xml;charset=utf8')
                ->withBody($stream);

            /** @var \stdClass $obj */
            $obj = $request->getParsedBody();
            $this->assertEquals('John', $obj->name);
        }
    }

    public function testGetParsedBodyXmlWithTextXMLMediaType()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $streamFactory = $provider->getStreamFactory();
            $stream = $streamFactory->createStream('<person><name>John</name></person>');

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request
                ->withHeader('Content-Type', 'text/xml')
                ->withBody($stream);

            /** @var \stdClass $obj */
            $obj = $request->getParsedBody();
            $this->assertEquals('John', $obj->name);
        }
    }

    public function testGetParsedBodyWithUnknownMediaTypeStructuredSyntaxSuffix()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request
                ->withHeader('Content-Type', 'text/foo+bar')
                ->withParsedBody(null);

            $this->assertNull($request->getParsedBody());
        }
    }

    /**
     * Will fail if a simple_xml warning is created
     */
    public function testInvalidXmlIsQuietForTextXml()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $streamFactory = $provider->getStreamFactory();
            $stream = $streamFactory->createStream('<person><name>John</name></invalid');

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request
                ->withHeader('Content-Type', 'text/xml')
                ->withBody($stream);

            $this->assertNull($request->getParsedBody());
        }
    }

    /**
     * Will fail if a simple_xml warning is created
     */
    public function testInvalidXmlIsQuietForApplicationXml()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $streamFactory = $provider->getStreamFactory();
            $stream = $streamFactory->createStream('<person><name>John</name></invalid');

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request
                ->withHeader('Content-Type', 'application/xml')
                ->withBody($stream);

            $this->assertNull($request->getParsedBody());
        }
    }

    public function testGetParameterFromBody()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request->withParsedBody(['foo' => 'bar']);
            $clone = $request->withParsedBody((object) ['foo' => 'bar']);

            $this->assertEquals('bar', $request->getParam('foo'));
            $this->assertEquals('bar', $clone->getParam('foo'));
        }
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetParsedBodyThrowsRuntimeExceptionWhenInvalidTypeReturned()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $streamFactory = $provider->getStreamFactory();
            $stream = $streamFactory->createStream('{"foo":"bar"}');

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request
                ->withHeader('Content-Type', 'application/json;charset=utf8')
                ->withBody($stream);

            $request->registerMediaTypeParser('application/json', function () {
                return 10;
            });

            $request->getParsedBody();
        }
    }

    public function testWithParsedBody()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request->withParsedBody([]);

            $this->assertEquals([], $request->getParsedBody());
        }
    }

    public function testWithParsedBodyNull()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request->withParsedBody(null);
            $this->assertNull($request->getParsedBody());
        }
    }

    public function testGetParameterFromBodyWithHelper()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('POST', 'https://google.com');
            $request = $request->withParsedBody(['foo' => 'bar']);
            $clone = $request->withParsedBody((object) ['foo' => 'bar']);

            $this->assertEquals('bar', $request->getParsedBodyParam('foo'));
            $this->assertEquals('bar', $clone->getParsedBodyParam('foo'));
        }
    }

    public function testGetQueryParam()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com?foo=bar');
            $this->assertEquals('bar', $request->getQueryParam('foo'));
        }
    }

    public function testGetQueryParamWithGetParam()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com?foo=bar');
            $this->assertEquals('bar', $request->getParam('foo'));
        }
    }

    public function testGetParams()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com?foo=bar&bar=baz');
            $this->assertEquals(['foo' => 'bar', 'bar' => 'baz'], $request->getParams());
        }
    }

    public function testGetParamsWithBodyPriority()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $streamFactory = $provider->getStreamFactory();
            $stream = $streamFactory->createStream('foo=bar&bar=baz');

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com?foo=baz&bar=foo');
            $request = $request
                ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
                ->withBody($stream);

            $this->assertEquals(['foo' => 'bar', 'bar' => 'baz'], $request->getParams());
        }
    }

    public function testGetParamFromBodyOverQuery()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $streamFactory = $provider->getStreamFactory();
            $stream = $streamFactory->createStream('foo=bar');

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com?foo=baz');
            $request = $request
                ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
                ->withBody($stream);

            $this->assertEquals('bar', $request->getParam('foo'));
        }
    }

    public function testGetParamWithDefaultFromBodyOverQuery()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $streamFactory = $provider->getStreamFactory();
            $stream = $streamFactory->createStream('foo=bar');

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com?foo=baz');
            $request = $request
                ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
                ->withBody($stream);

            $this->assertEquals('baz', $request->getParam('bar', 'baz'));
        }
    }

    public function testGetProtocolVersion()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withProtocolVersion('1.0');

            $this->assertEquals('1.0', $request->getProtocolVersion());
        }
    }

    public function testWithProtocolVersion()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withProtocolVersion('1.0');
            $clone = $request->withProtocolVersion('1.1');

            $this->assertEquals('1.0', $request->getProtocolVersion());
            $this->assertEquals('1.1', $clone->getProtocolVersion());
        }
    }

    public function testGetHeaders()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withHeader('Content-Type', 'application/json');

            $expectedHeaders = ['Content-Type' => ['application/json'], 'Host' => ['google.com']];
            $this->assertEquals($expectedHeaders, $request->getHeaders());
        }
    }

    public function testHasHeaders()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request->withHeader('Content-Type', 'application/json');

            $this->assertEquals(true, $request->hasHeader('Content-Type'));
        }
    }

    public function testWithAddedHeader()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request
                ->withHeader('Content-Type', 'application/json')
                ->withAddedHeader('Content-Type', 'application/xml');

            $this->assertEquals(['application/json', 'application/xml'], $request->getHeader('Content-Type'));
        }
    }

    public function testWithoutHeader()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedServerRequestFactory = new DecoratedServerRequestFactory($provider->getServerRequestFactory());

            $request = $decoratedServerRequestFactory->createServerRequest('GET', 'https://google.com');
            $request = $request
                ->withHeader('Content-Length', '150')
                ->withHeader('Content-Type', 'application/json');

            $this->assertEquals(true, $request->hasHeader('Content-Length'));

            $request = $request->withoutHeader('Content-Length');
            $this->assertEquals(false, $request->hasHeader('Content-Length'));
        }
    }
}
