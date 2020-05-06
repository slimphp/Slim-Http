<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Http;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /**
     * @var UriInterface
     */
    protected $uri;

    /**
     * @param UriInterface $uri
     */
    final public function __construct(UriInterface $uri)
    {
        $this->uri = $uri;
    }

    /**
     * Disable magic setter to ensure immutability
     * @param mixed $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthority(): string
    {
        return $this->uri->getAuthority();
    }

    /**
     * {@inheritdoc}
     */
    public function getFragment(): string
    {
        return $this->uri->getFragment();
    }

    /**
     * {@inheritdoc}
     */
    public function getHost(): string
    {
        return $this->uri->getHost();
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): string
    {
        return $this->uri->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getPort(): ?int
    {
        return $this->uri->getPort();
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery(): string
    {
        return $this->uri->getQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme(): string
    {
        return $this->uri->getScheme();
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo(): string
    {
        return $this->uri->getUserInfo();
    }

    /**
     * {@inheritdoc}
     */
    public function withFragment($fragment)
    {
        $uri = $this->uri->withFragment($fragment);
        return new static($uri);
    }

    /**
     * {@inheritdoc}
     */
    public function withHost($host)
    {
        $uri = $this->uri->withHost($host);
        return new static($uri);
    }

    /**
     * {@inheritdoc}
     */
    public function withPath($path)
    {
        $uri = $this->uri->withPath($path);
        return new static($uri);
    }

    /**
     * {@inheritdoc}
     */
    public function withPort($port)
    {
        $uri = $this->uri->withPort($port);
        return new static($uri);
    }

    /**
     * {@inheritdoc}
     */
    public function withQuery($query)
    {
        $uri = $this->uri->withQuery($query);
        return new static($uri);
    }

    /**
     * {@inheritdoc}
     */
    public function withScheme($scheme)
    {
        $uri = $this->uri->withScheme($scheme);
        return new static($uri);
    }

    /**
     * {@inheritdoc}
     */
    public function withUserInfo($user, $password = null)
    {
        $uri = $this->uri->withUserInfo($user, $password);
        return new static($uri);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->uri->__toString();
    }

    /**
     * Return the fully qualified base URL.
     *
     * Note that this method never includes a trailing slash
     *
     * This method is not part of PSR-7.
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        $scheme = $this->uri->getScheme();
        $authority = $this->uri->getAuthority();
        return ($scheme !== '' ? $scheme . ':' : '') . ($authority !== '' ? '//' . $authority : '');
    }
}
