<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Http;

use InvalidArgumentException;
use Slim\Http\Interfaces\ResponseInterface as DecoratedResponseInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

use function basename;
use function in_array;
use function is_array;
use function is_resource;
use function is_string;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use function mime_content_type;
use function preg_replace;
use function rawurlencode;
use function sprintf;
use function stream_get_meta_data;
use function strlen;
use function substr;

use const JSON_ERROR_NONE;

class Response implements DecoratedResponseInterface
{
    protected ResponseInterface $response;

    protected StreamFactoryInterface $streamFactory;

    /**
     * EOL characters used for HTTP response.
     *
     * @var string
     */
    public const EOL = "\r\n";

    /**
     * @param ResponseInterface $response
     * @param StreamFactoryInterface $streamFactory
     */
    final public function __construct(ResponseInterface $response, StreamFactoryInterface $streamFactory)
    {
        $this->response = $response;
        $this->streamFactory = $streamFactory;
    }

    /**
     * Disable magic setter to ensure immutability
     * @param mixed $name
     * @param mixed $value
     * @return void
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
    public function getHeader($name): array
    {
        return $this->response->getHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name): string
    {
        return $this->response->getHeaderLine($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
        return $this->response->getHeaders();
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion(): string
    {
        return $this->response->getProtocolVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase(): string
    {
        return $this->response->getReasonPhrase();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name): bool
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
     * @param  mixed     $data   The data
     * @param  int|null  $status The HTTP status code
     * @param  int       $options Json encoding options
     * @param  int       $depth Json encoding max depth
     * @return static
     */
    public function withJson($data, ?int $status = null, int $options = 0, int $depth = 512): ResponseInterface
    {
        $json = (string) json_encode($data, $options, $depth > 0 ? $depth : 512);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(json_last_error_msg(), json_last_error());
        }

        $response = $this->response
            ->withHeader('Content-Type', 'application/json')
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
     * @param string    $url The redirect destination.
     * @param int|null  $status The redirect HTTP status code.
     * @return static
     */
    public function withRedirect(string $url, ?int $status = null): ResponseInterface
    {
        $response = $this->response->withHeader('Location', $url);

        if ($status === null) {
            $status = 302;
        }
        $response = $response->withStatus($status);

        return new static($response, $this->streamFactory);
    }

    /**
     * Note: This method is not part of the PSR-7 standard.
     *
     * This method will trigger the client to download the specified file
     * It will append the `Content-Disposition` header to the response object
     *
     * @param string|resource|StreamInterface $file
     * @param string|null                     $name
     * @param bool|string                     $contentType
     *
     * @return static
     */
    public function withFileDownload($file, ?string $name = null, $contentType = true): ResponseInterface
    {
        $disposition = 'attachment';
        $fileName = $name;

        if (is_string($file) && $name === null) {
            $fileName = basename($file);
        }

        if ($name === null && (is_resource($file) || $file instanceof StreamInterface)) {
            $metaData = $file instanceof StreamInterface
                ? $file->getMetadata()
                : stream_get_meta_data($file);

            if (is_array($metaData) && isset($metaData['uri'])) {
                $uri = $metaData['uri'];
                if ('php://' !== substr($uri, 0, 6)) {
                    $fileName = basename($uri);
                }
            }
        }

        if (is_string($fileName) && strlen($fileName)) {
            /*
             * The regex used below is to ensure that the $fileName contains only
             * characters ranging from ASCII 128-255 and ASCII 0-31 and 127 are replaced with an empty string
             */
            $disposition .= '; filename="' . preg_replace('/[\x00-\x1F\x7F\"]/', ' ', $fileName) . '"';
            $disposition .= "; filename*=UTF-8''" . rawurlencode($fileName);
        }

        return $this
            ->withFile($file, $contentType)
            ->withHeader('Content-Disposition', $disposition);
    }

    /**
     * Note: This method is not part of the PSR-7 standard.
     *
     * This method prepares the response object to return a file response to the
     * client without `Content-Disposition` header which defaults to `inline`
     *
     * You control the behavior of the `Content-Type` header declaration via `$contentType`
     * Use a string to override the header to a value of your choice. e.g.: `application/json`
     * When set to `true` we attempt to detect the content type using `mime_content_type`
     * When set to `false`
     *
     * @param string|resource|StreamInterface $file
     * @param bool|string                     $contentType
     *
     * @return static
     *
     * @throws RuntimeException If the file cannot be opened.
     * @throws InvalidArgumentException If the mode is invalid.
     */
    public function withFile($file, $contentType = true): ResponseInterface
    {
        $response = $this->response;

        if (is_resource($file)) {
            $response = $response->withBody($this->streamFactory->createStreamFromResource($file));
        } elseif (is_string($file)) {
            $response = $response->withBody($this->streamFactory->createStreamFromFile($file));
        } elseif ($file instanceof StreamInterface) {
            $response = $response->withBody($file);
        } else {
            throw new InvalidArgumentException(
                'Parameter 1 of Response::withFile() must be a resource, a string ' .
                'or an instance of Psr\Http\Message\StreamInterface.'
            );
        }

        if ($contentType === true) {
            $contentType = is_string($file) ? mime_content_type($file) : 'application/octet-stream';
        }

        if (is_string($contentType)) {
            $response = $response->withHeader('Content-Type', $contentType);
        }

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
    public function write(string $data): ResponseInterface
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
        $output .= $this->response->getBody();

        return $output;
    }
}
