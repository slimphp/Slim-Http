<?php
namespace Slim\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

interface FactoryInterface
{
    public function makeRequest() : ServerRequestInterface;

    public function makeUri() : UriInterface;

    public function makeHeaders() : HeadersInterface;

    public function makeBody() : StreamInterface;
}
