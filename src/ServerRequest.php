<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Http;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;

use function array_merge;
use function count;
use function explode;
use function is_array;
use function is_null;
use function is_object;
use function json_decode;
use function libxml_clear_errors;
use function libxml_disable_entity_loader;
use function libxml_use_internal_errors;
use function parse_str;
use function preg_split;
use function property_exists;
use function simplexml_load_string;
use function strtolower;

use const LIBXML_VERSION;

class ServerRequest implements ServerRequestInterface
{
    protected ServerRequestInterface $serverRequest;

    protected array $bodyParsers;

    /**
     * @param ServerRequestInterface $serverRequest
     */
    final public function __construct(ServerRequestInterface $serverRequest)
    {
        $this->serverRequest = $serverRequest;

        $this->registerMediaTypeParser('application/json', function ($input) {
            $result = json_decode($input, true);

            if (!is_array($result)) {
                return null;
            }

            return $result;
        });

        $xmlParserCallable = function ($input) {
            $backup = self::disableXmlEntityLoader(true);
            $backup_errors = libxml_use_internal_errors(true);
            $result = simplexml_load_string($input);

            self::disableXmlEntityLoader($backup);
            libxml_clear_errors();
            libxml_use_internal_errors($backup_errors);

            if ($result === false) {
                return null;
            }

            return $result;
        };

        $this->registerMediaTypeParser('application/xml', $xmlParserCallable);
        $this->registerMediaTypeParser('text/xml', $xmlParserCallable);

        $this->registerMediaTypeParser('application/x-www-form-urlencoded', function ($input) {
            parse_str($input, $data);
            return $data;
        });
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
    public function getAttribute($name, $default = null)
    {
        return $this->serverRequest->getAttribute($name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes(): array
    {
        return $this->serverRequest->getAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function getBody(): StreamInterface
    {
        return $this->serverRequest->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParams(): array
    {
        return $this->serverRequest->getCookieParams();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name): array
    {
        return $this->serverRequest->getHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name): string
    {
        return $this->serverRequest->getHeaderLine($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
        return $this->serverRequest->getHeaders();
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod(): string
    {
        return $this->serverRequest->getMethod();
    }

    /**
     * {@inheritdoc}
     */
    public function getParsedBody()
    {
        $parsedBody = $this->serverRequest->getParsedBody();

        if (!empty($parsedBody)) {
            return $parsedBody;
        }

        $mediaType = $this->getMediaType();
        if ($mediaType === null) {
            return $parsedBody;
        }

        // Check if this specific media type has a parser registered first
        if (!isset($this->bodyParsers[$mediaType])) {
            // If not, look for a media type with a structured syntax suffix (RFC 6839)
            $parts = explode('+', $mediaType);
            if (count($parts) >= 2) {
                $mediaType = 'application/' . $parts[count($parts) - 1];
            }
        }

        if (isset($this->bodyParsers[$mediaType])) {
            $body = (string)$this->getBody();
            $parsed = $this->bodyParsers[$mediaType]($body);

            if (!is_null($parsed) && !is_object($parsed) && !is_array($parsed)) {
                throw new RuntimeException(
                    'Request body media type parser return value must be an array, an object, or null'
                );
            }

            return $parsed;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion(): string
    {
        return $this->serverRequest->getProtocolVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParams(): array
    {
        $queryParams = $this->serverRequest->getQueryParams();

        if (is_array($queryParams) && !empty($queryParams)) {
            return $queryParams;
        }

        $parsedQueryParams = [];
        parse_str($this->serverRequest->getUri()->getQuery(), $parsedQueryParams);

        return $parsedQueryParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestTarget(): string
    {
        return $this->serverRequest->getRequestTarget();
    }

    /**
     * {@inheritdoc}
     */
    public function getServerParams(): array
    {
        return $this->serverRequest->getServerParams();
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadedFiles(): array
    {
        return $this->serverRequest->getUploadedFiles();
    }

    /**
     * {@inheritdoc}
     */
    public function getUri(): UriInterface
    {
        return $this->serverRequest->getUri();
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name): bool
    {
        return $this->serverRequest->hasHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value)
    {
        $serverRequest = $this->serverRequest->withAddedHeader($name, $value);
        return new static($serverRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function withAttribute($name, $value)
    {
        $serverRequest = $this->serverRequest->withAttribute($name, $value);
        return new static($serverRequest);
    }

    /**
     * Create a new instance with the specified derived request attributes.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * This method allows setting all new derived request attributes as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * updated attributes.
     *
     * @param  array $attributes New attributes
     * @return static
     */
    public function withAttributes(array $attributes)
    {
        $serverRequest = $this->serverRequest;

        foreach ($attributes as $attribute => $value) {
            $serverRequest = $serverRequest->withAttribute($attribute, $value);
        }

        return new static($serverRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function withoutAttribute($name)
    {
        $serverRequest = $this->serverRequest->withoutAttribute($name);
        return new static($serverRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {
        $serverRequest = $this->serverRequest->withBody($body);
        return new static($serverRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function withCookieParams(array $cookies)
    {
        $serverRequest = $this->serverRequest->withCookieParams($cookies);
        return new static($serverRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value)
    {
        $serverRequest = $this->serverRequest->withHeader($name, $value);
        return new static($serverRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name)
    {
        $serverRequest = $this->serverRequest->withoutHeader($name);
        return new static($serverRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function withMethod($method)
    {
        $serverRequest = $this->serverRequest->withMethod($method);
        return new static($serverRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function withParsedBody($data)
    {
        $serverRequest = $this->serverRequest->withParsedBody($data);
        return new static($serverRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version)
    {
        $serverRequest = $this->serverRequest->withProtocolVersion($version);
        return new static($serverRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function withQueryParams(array $query)
    {
        $serverRequest = $this->serverRequest->withQueryParams($query);
        return new static($serverRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function withRequestTarget($requestTarget)
    {
        $serverRequest = $this->serverRequest->withRequestTarget($requestTarget);
        return new static($serverRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $serverRequest = $this->serverRequest->withUploadedFiles($uploadedFiles);
        return new static($serverRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $serverRequest = $this->serverRequest->withUri($uri, $preserveHost);
        return new static($serverRequest);
    }

    /**
     * Get serverRequest content character set, if known.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return string|null
     */
    public function getContentCharset(): ?string
    {
        $mediaTypeParams = $this->getMediaTypeParams();

        if (isset($mediaTypeParams['charset'])) {
            return $mediaTypeParams['charset'];
        }

        return null;
    }

    /**
     * Get serverRequest content type.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return string|null The serverRequest content type, if known
     */
    public function getContentType(): ?string
    {
        $result = $this->serverRequest->getHeader('Content-Type');
        return $result ? $result[0] : null;
    }

    /**
     * Get serverRequest content length, if known.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return int|null
     */
    public function getContentLength(): ?int
    {
        $result = $this->serverRequest->getHeader('Content-Length');
        return $result ? (int) $result[0] : null;
    }

    /**
     * Fetch cookie value from cookies sent by the client to the server.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param string $key     The attribute name.
     * @param mixed  $default Default value to return if the attribute does not exist.
     *
     * @return mixed
     */
    public function getCookieParam(string $key, $default = null)
    {
        $cookies = $this->serverRequest->getCookieParams();
        $result = $default;

        if (isset($cookies[$key])) {
            $result = $cookies[$key];
        }

        return $result;
    }

    /**
     * Get serverRequest media type, if known.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return string|null The serverRequest media type, minus content-type params
     */
    public function getMediaType(): ?string
    {
        $contentType = $this->getContentType();

        if ($contentType) {
            $contentTypeParts = preg_split('/\s*[;,]\s*/', $contentType);
            if ($contentTypeParts === false) {
                return null;
            }
            return strtolower($contentTypeParts[0]);
        }

        return null;
    }

    /**
     * Get serverRequest media type params, if known.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return string[]
     */
    public function getMediaTypeParams(): array
    {
        $contentType = $this->getContentType();
        $contentTypeParams = [];

        if ($contentType) {
            $contentTypeParts = preg_split('/\s*[;,]\s*/', $contentType);
            if ($contentTypeParts !== false) {
                $contentTypePartsLength = count($contentTypeParts);
                for ($i = 1; $i < $contentTypePartsLength; $i++) {
                    $paramParts = explode('=', $contentTypeParts[$i]);
                    /** @var string[] $paramParts */
                    $contentTypeParams[strtolower($paramParts[0])] = $paramParts[1];
                }
            }
        }

        return $contentTypeParams;
    }

    /**
     * Fetch serverRequest parameter value from body or query string (in that order).
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param  string $key The parameter key.
     * @param  mixed  $default The default value.
     *
     * @return mixed The parameter value.
     */
    public function getParam(string $key, $default = null)
    {
        $postParams = $this->getParsedBody();
        $getParams = $this->getQueryParams();
        $result = $default;

        if (is_array($postParams) && isset($postParams[$key])) {
            $result = $postParams[$key];
        } elseif (is_object($postParams) && property_exists($postParams, $key)) {
            $result = $postParams->$key;
        } elseif (isset($getParams[$key])) {
            $result = $getParams[$key];
        }

        return $result;
    }

    /**
     * Fetch associative array of body and query string parameters.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return array
     */
    public function getParams(): array
    {
        $params = $this->getQueryParams();
        $postParams = $this->getParsedBody();

        if ($postParams) {
            $params = array_merge($params, (array)$postParams);
        }

        return $params;
    }

    /**
     * Fetch parameter value from serverRequest body.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getParsedBodyParam(string $key, $default = null)
    {
        $postParams = $this->getParsedBody();
        $result = $default;

        if (is_array($postParams) && isset($postParams[$key])) {
            $result = $postParams[$key];
        } elseif (is_object($postParams) && property_exists($postParams, $key)) {
            $result = $postParams->$key;
        }

        return $result;
    }

    /**
     * Fetch parameter value from query string.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getQueryParam(string $key, $default = null)
    {
        $getParams = $this->getQueryParams();
        $result = $default;

        if (isset($getParams[$key])) {
            $result = $getParams[$key];
        }

        return $result;
    }

    /**
     * Retrieve a server parameter.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function getServerParam(string $key, $default = null)
    {
        $serverParams = $this->serverRequest->getServerParams();
        return $serverParams[$key] ?? $default;
    }

    /**
     * Register media type parser.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param string   $mediaType A HTTP media type (excluding content-type params).
     * @param callable $callable  A callable that returns parsed contents for media type.
     * @return static
     */
    public function registerMediaTypeParser(string $mediaType, callable $callable): ServerRequestInterface
    {
        if ($callable instanceof Closure) {
            $callable = $callable->bindTo($this);
        }

        $this->bodyParsers[$mediaType] = $callable;

        return $this;
    }

    /**
     * Is this a DELETE serverRequest?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->isMethod('DELETE');
    }

    /**
     * Is this a GET serverRequest?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->isMethod('GET');
    }

    /**
     * Is this a HEAD serverRequest?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isHead(): bool
    {
        return $this->isMethod('HEAD');
    }

    /**
     * Does this serverRequest use a given method?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param  string $method HTTP method
     * @return bool
     */
    public function isMethod(string $method): bool
    {
        return $this->serverRequest->getMethod() === $method;
    }

    /**
     * Is this a OPTIONS serverRequest?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isOptions(): bool
    {
        return $this->isMethod('OPTIONS');
    }

    /**
     * Is this a PATCH serverRequest?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isPatch(): bool
    {
        return $this->isMethod('PATCH');
    }

    /**
     * Is this a POST serverRequest?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->isMethod('POST');
    }

    /**
     * Is this a PUT serverRequest?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isPut(): bool
    {
        return $this->isMethod('PUT');
    }

    /**
     * Is this an XHR serverRequest?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isXhr(): bool
    {
        return $this->serverRequest->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    private static function disableXmlEntityLoader(bool $disable): bool
    {
        if (LIBXML_VERSION >= 20900) {
            // libxml >= 2.9.0 disables entity loading by default, so it is
            // safe to skip the real call (deprecated in PHP 8).
            return true;
        }

        // @codeCoverageIgnoreStart
        return libxml_disable_entity_loader($disable);
        // @codeCoverageIgnoreEnd
    }
}
