<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2018 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Http\Factory;

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Slim\Http\Decorators\UriDecorator;

/**
 * Class DecoratedUriFactory
 * @package Slim\Http\Factory
 */
class DecoratedUriFactory implements UriFactoryInterface
{
    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;

    /**
     * DecoratedUriFactory constructor.
     * @param UriFactoryInterface $uriFactory
     */
    public function __construct(UriFactoryInterface $uriFactory)
    {
        $this->uriFactory = $uriFactory;
    }

    /**
     * @param string $uri
     * @return UriDecorator
     */
    public function createUri(string $uri = ''): UriInterface
    {
        $uri = $this->uriFactory->createUri($uri);
        return new UriDecorator($uri);
    }
}
