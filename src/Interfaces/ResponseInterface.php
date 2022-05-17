<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Http\Interfaces;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

interface ResponseInterface extends PsrResponseInterface
{
    /**
     * Write JSON to Response Body.
     *
     * This method prepares the response object to return an HTTP Json
     * response to the client.
     *
     * @param mixed     $data   The data
     * @param int|null  $status The HTTP status code
     * @param int       $options JSON encoding options
     * @param int       $depth  JSON encoding max depth
     * @return PsrResponseInterface
     */
    public function withJson($data, ?int $status = null, int $options = 0, int $depth = 512): PsrResponseInterface;

    /**
     * Redirect to specified location
     *
     * This method prepares the response object to return an HTTP Redirect
     * response to the client.
     *
     * @param string    $url    The redirect destination.
     * @param int|null  $status The redirect HTTP status code.
     * @return PsrResponseInterface
     */
    public function withRedirect(string $url, ?int $status = null): PsrResponseInterface;

    /**
     * This method will trigger the client to download the specified file
     * It will append the `Content-Disposition` header to the response object
     *
     * @param mixed         $file
     * @param string|null   $name
     * @param bool|string   $contentType
     * @return PsrResponseInterface
     */
    public function withFileDownload($file, ?string $name = null, $contentType = true): PsrResponseInterface;

    /**
     * This method prepares the response object to return a file response to the
     * client without `Content-Disposition` header which defaults to `inline`
     *
     * @param mixed         $file
     * @param bool|string   $contentType
     * @return PsrResponseInterface
     */
    public function withFile($file, $contentType = true): PsrResponseInterface;

    /**
     * Write data to the response body.
     *
     * @param string $data
     * @return PsrResponseInterface
     */
    public function write(string $data): PsrResponseInterface;

    /**
     * Is this response a client error?
     *
     * @return bool
     */
    public function isClientError(): bool;

    /**
     * Is this response empty?
     *
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Is this response forbidden?
     *
     * @return bool
     */
    public function isForbidden(): bool;

    /**
     * Is this response informational?
     *
     * @return bool
     */
    public function isInformational(): bool;

    /**
     * Is this response OK?
     *
     * @return bool
     */
    public function isOk(): bool;

    /**
     * Is this response not Found?
     *
     * @return bool
     */
    public function isNotFound(): bool;

    /**
     * Is this response a redirect?
     *
     * @return bool
     */
    public function isRedirect(): bool;

    /**
     * Is this response a redirection?
     *
     * @return bool
     */
    public function isRedirection(): bool;

    /**
     * Is this response a server error?
     *
     * @return bool
     */
    public function isServerError(): bool;

    /**
     * Is this response successful?
     *
     * @return bool
     */
    public function isSuccessful(): bool;

    /**
     * Convert response to string.
     * @return string
     */
    public function __toString(): string;
}
