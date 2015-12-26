<?php
namespace Slim\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

interface FactoryInterface
{
    public function makeRequest(array $globals) : ServerRequestInterface;

    public function makeUri(array $globals) : UriInterface;

    public function makeHeaders(array $globals) : HeadersInterface;

    public function makeBody() : StreamInterface;
}
