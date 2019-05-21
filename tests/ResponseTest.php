<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Http;

use Psr\Http\Message\StreamFactoryInterface;
use RuntimeException;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Http\File;
use Slim\Http\Response;
use Slim\Tests\Http\Providers\Psr17FactoryProvider;

class ResponseTest extends TestCase
{
    public function testDisableSetter()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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

    public function testIsEmpty()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
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
            $provider = new $factoryProvider;
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            /** @var Response $originalResponse */
            $originalResponse = $decoratedResponseFactory->createResponse(503);
            $response = $originalResponse->withJson($data, 201);

            $this->assertNotEquals($response->getStatusCode(), $originalResponse->getStatusCode());
            $this->assertEquals(201, $response->getStatusCode());
            $this->assertEquals('application/json;charset=utf-8', $response->getHeaderLine('Content-Type'));

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
            $provider = new $factoryProvider;
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $provider->getResponseFactory(),
                $provider->getStreamFactory()
            );

            $response = $decoratedResponseFactory->createResponse(400);
            $response->withJson($data, 200);
        }
    }

    /**
     * Provide file downloads and their expected values.
     *
     * @return array
     */
    public function provideFileDownloads(): array
    {
        return [
            [
                function (Response $response, StreamFactoryInterface $streamFactory): Response {
                    return $response->withFileDownload(File::fromPath(__DIR__.'/Assets/plain.txt'));
                },
                '12345678',
                ['attachment; filename="plain.txt"'],
                ['text/plain'],
            ],
            [
                function (Response $response, StreamFactoryInterface $streamFactory): Response {
                    return $response->withFileDownload(
                        File::fromStream($streamFactory->createStream('1234'), 'stream.txt')
                    );
                },
                '1234',
                ['attachment; filename="stream.txt"'],
                ['text/plain'],
            ],
        ];
    }

    /**
     * @dataProvider provideFileDownloads
     *
     * @param callable $responseCallback
     * @param string   $expectedBodyContents
     * @param array    $expectedContentDisposition
     * @param array    $expectedContentType
     */
    public function testWithFileDownload(
        callable $responseCallback,
        string $expectedBodyContents,
        array $expectedContentDisposition,
        array $expectedContentType
    ) {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider;

            $responseFactory = $provider->getResponseFactory();
            $streamFactory = $provider->getStreamFactory();
            $decoratedResponseFactory = new DecoratedResponseFactory(
                $responseFactory,
                $streamFactory
            );

            /** @var Response $response */
            $response = call_user_func(
                $responseCallback,
                $decoratedResponseFactory->createResponse(200),
                $streamFactory
            );

            $body = $response->getBody();
            $body->rewind();
            $bodyContents = $body->getContents();

            $this->assertEquals($expectedBodyContents, $bodyContents);
            $this->assertEquals($expectedContentDisposition, $response->getHeader('Content-Disposition'));
            $this->assertEquals($expectedContentType, $response->getHeader('Content-Type'));
        }
    }
}
