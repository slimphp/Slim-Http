<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Http\Factory;

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Slim\Http\Uri;

class DecoratedUriFactory implements UriFactoryInterface
{
    /**
     * @var UriFactoryInterface
     */
    protected $uriFactory;

    /**
     * @param UriFactoryInterface $uriFactory
     */
    public function __construct(UriFactoryInterface $uriFactory)
    {
        $this->uriFactory = $uriFactory;
    }

    /**
     * @param string $uri
     * @return Uri
     */
    public function createUri(string $uri = ''): UriInterface
    {
        $uri = $this->uriFactory->createUri($uri);
        return new Uri($uri);
    }
}
