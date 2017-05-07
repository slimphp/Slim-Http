<?php
namespace Slim\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

interface FactoryInterface
{
    /**
     * @param array $globals
     * @return ServerRequestInterface
     */
    public function makeRequest(array $globals);

    /**
     * @param array $globals
     * @return UriInterface
     */
    public function makeUri(array $globals);

    /**
     * @param array $globals
     * @return HeadersInterface
     */
    public function makeHeaders(array $globals);

    /**
     * @return StreamInterface
     */
    public function makeBody();
}
