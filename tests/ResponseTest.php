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
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Http\Response;
use Slim\Tests\Http\Providers\Psr17FactoryProvider;

class ResponseTest extends TestCase
{
    public function testDisableSetter()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse();
            $response->foo = 'bar';

            $this->assertFalse(property_exists($response, 'foo'));
        }
    }

    public function testGetHeader()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse();
            $response = $response->withHeader('Content-Type', 'application/json');

            $this->assertEquals(['application/json'], $response->getHeader('Content-Type'));
        }
    }

    public function testGetStatusCode()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse(404);
            $this->assertEquals(404, $response->getStatusCode());
        }
    }

    public function testWithStatus()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse();
            $clone = $response->withStatus(404);

            $this->assertEquals(200, $response->getStatusCode());
            $this->assertEquals(404, $clone->getStatusCode());
        }
    }

    public function testGetReasonPhrase()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse(404, 'Not Found');
            $this->assertEquals('Not Found', $response->getReasonPhrase());
        }
    }

    public function testGetCustomReasonPhrase()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse(200, 'Custom Phrase');
            $this->assertEquals('Custom Phrase', $response->getReasonPhrase());
        }
    }

    public function testWithAddedHeader()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse();
            $response = $response->withHeader('Content-Type', 'application/json');
            $response = $response->withAddedHeader('Content-Type', 'application/pdf');

            $this->assertEquals(['application/json', 'application/pdf'], $response->getHeader('Content-Type'));
        }
    }

    public function testWithoutHeader()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse();

            $response = $response->withHeader('Content-Type', 'application/json');
            $this->assertEquals(['application/json'], $response->getHeader('Content-Type'));

            $response = $response->withoutHeader('Content-Type');
            $this->assertEquals([], $response->getHeader('Content-Type'));
        }
    }

    public function testWithProtocolVersion()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse();

            $response = $response->withProtocolVersion('1.0');
            $this->assertEquals('1.0', $response->getProtocolVersion());
        }
    }

    public function testWithRedirect()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse();
            $clone = $response->withRedirect('/foo', 301);
            $cloneWithDefaultStatus = $response->withRedirect('/foo');
            $cloneWithStatusMethod = $response->withStatus(301)->withRedirect('/foo');

            $this->assertSame(200, $response->getStatusCode());
            $this->assertFalse($response->hasHeader('Location'));

            $this->assertSame(301, $clone->getStatusCode());
            $this->assertTrue($clone->hasHeader('Location'));
            $this->assertEquals('/foo', $clone->getHeaderLine('Location'));

            $this->assertSame(302, $cloneWithDefaultStatus->getStatusCode());
            $this->assertTrue($cloneWithDefaultStatus->hasHeader('Location'));
            $this->assertEquals('/foo', $cloneWithDefaultStatus->getHeaderLine('Location'));

            $this->assertSame(302, $cloneWithStatusMethod->getStatusCode());
            $this->assertTrue($cloneWithStatusMethod->hasHeader('Location'));
            $this->assertEquals('/foo', $cloneWithStatusMethod->getHeaderLine('Location'));
        }
    }

    public function fileProvider()
    {
        return [
            'with resource and content type specified' => [
                'text/plain',
                'resource',
                'Hello World',
                'text/plain',
            ],
            'with resource and content type auto-detection on' => [
                true,
                'resource',
                'Hello World',
                'application/octet-stream',
            ],
            'with resource and content type auto-detection off' => [
                false,
                'resource',
                'Hello World',
                '',
            ],
            'with string and content type specified' => [
                'text/plain',
                'string',
                'Hello World',
                'text/plain',
            ],
            'with string and content type auto-detection on' => [
                true,
                'string',
                'Hello World',
                'text/plain',
            ],
            'with string and content type auto-detection off' => [
                false,
                'string',
                'Hello World',
                '',
            ],
            'with stream and content type specified' => [
                'text/plain',
                'stream',
                'Hello World',
                'text/plain',
            ],
            'with stream and content type auto-detection on' => [
                true,
                'stream',
                'Hello World',
                'application/octet-stream',
            ],
            'with stream and content type auto-detection off' => [
                false,
                'stream',
                'Hello World',
                '',
            ],
        ];
    }

    /**
     * @dataProvider fileProvider
     * @param bool|string $contentType
     * @param string      $openAs
     * @param string      $expectedBody
     * @param string      $expectedContentType
     */
    public function testWithFile($contentType, string $openAs, string $expectedBody, string $expectedContentType)
    {
        $path = __DIR__ . '/Assets/plain.txt';

        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();

            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            switch ($openAs) {
                case 'resource':
                    $file = fopen($path, 'r');
                    break;

                case 'stream':
                    $file = $provider->getStreamFactory()->createStreamFromFile($path);
                    break;

                default:
                case 'string':
                    $file = $path;
                    break;
            }

            $response = $decoratedResponseFactory
                ->createResponse()
                ->withFile($file, $contentType);

            $this->assertEquals($expectedBody, (string) $response->getBody());
            $this->assertEquals($expectedContentType, $response->getHeaderLine('Content-Type'));

            if (is_resource($file)) {
                fclose($file);
            }
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWithFileThrowsInvalidArgumentException()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();

            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $decoratedResponseFactory->createResponse()->withFile(1);
        }
    }

    public function fileDownloadProvider()
    {
        return [
            'with resource and file name specified' => [
                'plain.txt',
                'resource',
                'attachment; filename="plain.txt"; filename*=UTF-8\'\'plain.txt',
            ],
            'with resource and file name not specified' => [
                null,
                'resource',
                'attachment; filename="plain.txt"; filename*=UTF-8\'\'plain.txt',
            ],
            'with string and file name specified' => [
                'plain.txt',
                'string',
                'attachment; filename="plain.txt"; filename*=UTF-8\'\'plain.txt',
            ],
            'with string and file name not specified' => [
                null,
                'string',
                'attachment; filename="plain.txt"; filename*=UTF-8\'\'plain.txt',
            ],
            'with stream and file name specified' => [
                'plain.txt',
                'stream',
                'attachment; filename="plain.txt"; filename*=UTF-8\'\'plain.txt',
            ],
            'with stream and file name not specified' => [
                null,
                'stream',
                'attachment; filename="plain.txt"; filename*=UTF-8\'\'plain.txt',
            ],
        ];
    }

    /**
     * @dataProvider fileDownloadProvider
     * @param string|null $name
     * @param string      $openAs
     * @param string      $expectedContentDisposition
     */
    public function testWithFileDownload(?string $name, string $openAs, string $expectedContentDisposition)
    {
        $path = __DIR__ . '/Assets/plain.txt';

        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();

            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            switch ($openAs) {
                case 'resource':
                    $file = fopen($path, 'r');
                    break;

                case 'stream':
                    $file = $provider->getStreamFactory()->createStreamFromFile($path);
                    break;

                default:
                case 'string':
                    $file = $path;
                    break;
            }

            $response = $decoratedResponseFactory
                ->createResponse()
                ->withFileDownload($file, $name);

            $this->assertEquals($expectedContentDisposition, $response->getHeaderLine('Content-Disposition'));

            if (is_resource($file)) {
                fclose($file);
            }
        }
    }

    public function testIsEmpty()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse(204);

            $this->assertTrue($response->isEmpty());
        }
    }

    public function testIsInformational()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse(100);

            $this->assertTrue($response->isInformational());
        }
    }

    public function testIsOk()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse();

            $this->assertTrue($response->isOk());
        }
    }

    public function testIsSuccessful()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse(201);
            $this->assertTrue($response->isSuccessful());
        }
    }

    public function testIsRedirect()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse(302);
            $this->assertTrue($response->isRedirect());
        }
    }

    public function testIsRedirection()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse(308);
            $this->assertTrue($response->isRedirection());
        }
    }

    public function testIsForbidden()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse(403);
            $this->assertTrue($response->isForbidden());
        }
    }

    public function testIsNotFound()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse(404);
            $this->assertTrue($response->isNotFound());
        }
    }

    public function testIsClientError()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse(400);
            $this->assertTrue($response->isClientError());
        }
    }

    public function testIsServerError()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse(503);
            $this->assertTrue($response->isServerError());
        }
    }

    public function testToString()
    {
        $output = 'HTTP/1.1 404 Not Found' . Response::EOL .
            'X-Foo: Bar' . Response::EOL . Response::EOL .
            'Where am I?';

        $expectedOutputString = '';
        $actualOutputString = '';

        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse(308);
            $response = $response->withStatus(404)->withHeader('X-Foo', 'Bar')->write('Where am I?');

            $expectedOutputString .= $output;
            $actualOutputString .= (string) $response;
        }

        $this->assertEquals($expectedOutputString, $actualOutputString);
    }

    public function testWithJson()
    {
        $data = ['foo' => 'bar1&bar2'];

        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            /** @var Response $originalResponse */
            $originalResponse = $decoratedResponseFactory->createResponse(503);
            $response = $originalResponse->withJson($data, 201);

            $this->assertNotEquals($response->getStatusCode(), $originalResponse->getStatusCode());
            $this->assertEquals(201, $response->getStatusCode());
            $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));

            $body = $response->getBody();
            $body->rewind();
            $dataJson = $body->getContents(); //json_decode($body->getContents(), true);

            $originalBody = $originalResponse->getBody();
            $originalBody->rewind();
            $originalContents = $originalBody->getContents();

            // test the original body hasn't be replaced
            $this->assertNotEquals($dataJson, $originalContents);
            $this->assertEquals('{"foo":"bar1&bar2"}', $dataJson);
            $this->assertEquals($data['foo'], json_decode($dataJson, true)['foo']);

            $response = $response->withJson($data, 200, JSON_HEX_AMP);

            $body = $response->getBody();
            $body->rewind();
            $dataJson = $body->getContents();

            $this->assertEquals('{"foo":"bar1\u0026bar2"}', $dataJson);
            $this->assertEquals($data['foo'], json_decode($dataJson, true)['foo']);

            $response = $response->withStatus(201)->withJson([]);
            $this->assertEquals($response->getStatusCode(), 201);
        }
    }

    /**
     * @expectedException RuntimeException
     */
    public function testWithInvalidJsonThrowsException()
    {
        $data = ['foo' => 'bar'.chr(233)];
        $this->assertEquals('bar'.chr(233), $data['foo']);

        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse(400);
            $response->withJson($data, 200);
        }
    }
}
