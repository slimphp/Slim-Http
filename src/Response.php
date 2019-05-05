<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class Response implements ResponseInterface
{
    /**
     * @var ResponseInterface;
     */
    private $response;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * EOL characters used for HTTP response.
     *
     * @var string
     */
    const EOL = "\r\n";

    /**
     * @param ResponseInterface $response
     * @param StreamFactoryInterface $streamFactory
     */
    public function __construct(ResponseInterface $response, StreamFactoryInterface $streamFactory)
    {
        $this->response = $response;
        $this->streamFactory = $streamFactory;
    }

    /**
     * Disable magic setter to ensure immutability
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->response->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name)
    {
        return $this->response->getHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name)
    {
        return $this->response->getHeaderLine($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->response->getHeaders();
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->response->getProtocolVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        return $this->response->getReasonPhrase();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name)
    {
        return $this->response->hasHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value)
    {
        $response = $this->response->withAddedHeader($name, $value);
        return new static($response, $this->streamFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {
        $response = $this->response->withBody($body);
        return new static($response, $this->streamFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value)
    {
        $response = $this->response->withHeader($name, $value);
        return new static($response, $this->streamFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name)
    {
        $response = $this->response->withoutHeader($name);
        return new static($response, $this->streamFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version)
    {
        $response = $this->response->withProtocolVersion($version);
        return new static($response, $this->streamFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $response = $this->response->withStatus($code, $reasonPhrase);
        return new static($response, $this->streamFactory);
    }

    /**
     * Write JSON to Response Body.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * This method prepares the response object to return an HTTP Json
     * response to the client.
     *
     * @param  mixed  $data   The data
     * @param  int    $status The HTTP status code.
     * @param  int    $options Json encoding options
     * @param  int    $depth Json encoding max depth
     * @return static
     */
    public function withJson($data, int $status = null, int $options = 0, int $depth = 512): ResponseInterface
    {
        $json = (string) json_encode($data, $options, $depth);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(json_last_error_msg(), json_last_error());
        }

        $response = $this->response
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->withBody($this->streamFactory->createStream($json));

        if ($status !== null) {
            $response = $response->withStatus($status);
        }

        return new static($response, $this->streamFactory);
    }

    /**
     * Redirect to specified location
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * This method prepares the response object to return an HTTP Redirect
     * response to the client.
     *
     * @param string $url The redirect destination.
     * @param int|null $status The redirect HTTP status code.
     * @return static
     */
    public function withRedirect(string $url, $status = null): ResponseInterface
    {
        $response = $this->response->withHeader('Location', $url);

        if ($status === null) {
            $status = 302;
        }
        $response = $response->withStatus($status);

        return new static($response, $this->streamFactory);
    }

    /**
     * Write data to the response body.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * Proxies to the underlying stream and writes the provided data to it.
     *
     * @param string $data
     * @return static
     */
    public function write($data): ResponseInterface
    {
        $this->response->getBody()->write($data);
        return $this;
    }

    /**
     * Is this response a client error?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isClientError(): bool
    {
        return $this->response->getStatusCode() >= 400 && $this->response->getStatusCode() < 500;
    }

    /**
     * Is this response empty?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return in_array($this->response->getStatusCode(), [204, 205, 304]);
    }

    /**
     * Is this response forbidden?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     * @api
     */
    public function isForbidden(): bool
    {
        return $this->response->getStatusCode() === 403;
    }

    /**
     * Is this response informational?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isInformational(): bool
    {
        return $this->response->getStatusCode() >= 100 && $this->response->getStatusCode() < 200;
    }

    /**
     * Is this response OK?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->response->getStatusCode() === 200;
    }

    /**
     * Is this response not Found?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isNotFound(): bool
    {
        return $this->response->getStatusCode() === 404;
    }

    /**
     * Is this response a redirect?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isRedirect(): bool
    {
        return in_array($this->response->getStatusCode(), [301, 302, 303, 307, 308]);
    }

    /**
     * Is this response a redirection?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isRedirection(): bool
    {
        return $this->response->getStatusCode() >= 300 && $this->response->getStatusCode() < 400;
    }

    /**
     * Is this response a server error?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isServerError(): bool
    {
        return $this->response->getStatusCode() >= 500 && $this->response->getStatusCode() < 600;
    }

    /**
     * Is this response successful?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->response->getStatusCode() >= 200 && $this->response->getStatusCode() < 300;
    }

    /**
     * Convert response to string.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return string
     */
    public function __toString(): string
    {
        $output = sprintf(
            'HTTP/%s %s %s%s',
            $this->response->getProtocolVersion(),
            $this->response->getStatusCode(),
            $this->response->getReasonPhrase(),
            self::EOL
        );

        foreach ($this->response->getHeaders() as $name => $values) {
            $output .= sprintf('%s: %s', $name, $this->response->getHeaderLine($name)) . self::EOL;
        }

        $output .= self::EOL;
        $output .= (string) $this->response->getBody();

        return $output;
    }
}
