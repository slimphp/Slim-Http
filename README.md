# Slim-Http

Slim PSR-7 Object Decorators

[![Build Status](https://travis-ci.org/slimphp/Slim-Http.svg?branch=master)](https://travis-ci.org/slimphp/Slim-Http)
[![Coverage Status](https://coveralls.io/repos/slimphp/Slim-Http/badge.svg?branch=master&service=github)](https://coveralls.io/github/slimphp/Slim-Http?branch=master)
[![Total Downloads](https://poser.pugx.org/slim/http/downloads)](https://packagist.org/packages/slim/http)
[![License](https://poser.pugx.org/slim/http/license)](https://packagist.org/packages/slim/http)

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install 
this library.

```bash
$ composer require slim/http "^0.5"
```

This will install the `slim/http` component and all required dependencies.
PHP 7.1, or newer, is required.

## Tests

To execute the test suite, you'll need to install all development dependencies.

```bash
$ git clone https://github.com/slimphp/Slim-Http
$ composer install
$ composer test
```


## Usage 

The Decoration Repo Provides 3 Factories which instantiate the Decorators. They respectively return PSR-7 Compatible Interfaces.
- `DecoratedResponseFactory`
- `DecoratedServerRequestFactory`
- `DecoratedUriFactory`

## Example for Instantiating a Decorated Nyholm/Psr7 Response
```php
<?php

use Nyholm\Psr7\Factory\Psr17Factory;
use Slim\Http\Factory\DecoratedResponseFactory;

$nyholmFactory = new Psr17Factory();

/**
 * DecoratedResponseFactory takes 2 parameters
 * @param \Psr\Http\Message\ResponseFactoryInterface which should be a ResponseFactory originating from the PSR-7 Implementation of your choice
 * @param \Psr\Http\Message\StreamFactoryInterface which should be a StreamFactory originating from the PSR-7 Implementation of your choice
 * Note: Nyholm/Psr17 has one factory which implements Both ResponseFactoryInterface and StreamFactoryInterface see https://github.com/Nyholm/psr7/blob/master/src/Factory/Psr17Factory.php
 */
$decoratedResponseFactory = new DecoratedResponseFactory($nyholmFactory, $nyholmFactory);

/**
 * @var \Slim\Http\Response $response
 * The returned variable is a Response which has methods like withJson()
 */
$response = $decoratedResponseFactory->createResponse(200, 'OK');
$response = $response->withJson(['data' => [1, 2, 3]]);

```


## Example for Instantiating a Decorated Zend Diactoros Response
```php
<?php

use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\StreamFactory;
use Slim\Http\Factory\DecoratedResponseFactory;

$responseFactory = new ResponseFactory();
$streamFactory = new StreamFactory();

/**
 * DecoratedResponseFactory takes 2 parameters
 * @param \Psr\Http\Message\ResponseFactoryInterface which should be a ResponseFactory originating from the PSR-7 Implementation of your choice
 * @param \Psr\Http\Message\StreamFactoryInterface which should be a StreamFactory originating from the PSR-7 Implementation of your choice
 */
$decoratedResponseFactory = new DecoratedResponseFactory($responseFactory, $streamFactory);

/**
 * @var \Slim\Http\Response $response
 * The returned variable is a Response which has methods like withJson()
 */
$response = $decoratedResponseFactory->createResponse(200, 'OK');
$response = $response->withJson(['data' => [1, 2, 3]]);

```


## Decoratored Response Object Methods
The decorated `ResponseInterface` provides the following additional methods:

#### `Response::withJson($data, $status, $options, $depth)` ####
| Parameter   | Type    | Description             |
|-------------|---------|-------------------------|
| **$data**   | `mixed` | The data to encode      |
| **$status** | `int`   | The HTTP Status Code    |
| **$depth**  | `int`   | JSON encoding max depth |

#### `Response::withFileDownload($file, $fileName, $headers)` ####
| Parameter     | Type    | Description                  |
|---------------|---------|------------------------------|
| **$file**     | `FileInterface` | The file to send     |
| **$fileName** | `string`        | The file name        |
| **$headers**  | `array`         | The HTTP headers     |

#### `Response::withFileStream($file, $fileName, $headers)` ####
| Parameter     | Type    | Description                  |
|---------------|---------|------------------------------|
| **$file**     | `FileInterface` | The file to send     |
| **$fileName** | `string`        | The file name        |
| **$headers**  | `array`         | The HTTP headers     |

#### `Response::withRedirect($url, $status)` ####
| Parameter   | Type     | Description                  |
|-------------|----------|------------------------------|
| **$url**    | `string` | The redirect destination url |
| **$status** | `int`    | The HTTP Status Code         |

#### `Response::write($data)` ####
| Parameter | Type     | Description                              |
|-----------|----------|------------------------------------------|
| **$url**  | `string` | The data to write to the `Response` body |

#### `Response::isClientError()` ####
Assert the underlying response's status code is between **400** and **500**.

#### `Response::isEmpty()` ####
Assert the underlying response's status code is **204, 205** or **304**.

#### `Response::isForbidden()` ####
Assert the underlying response's status code is **403**.

#### `Response::isInformational()` ####
Assert the underlying response's status code is between **100** and **200**.

#### `Response::isOk()` ####
Assert the underlying response's status code is **200**.

#### `Response::isNotFound()` ####
Assert the underlying response's status code is **404**.

#### `Response::isRedirection()` ####
Assert the underlying response's status code is between **300** and **400**.

#### `Response::isServerError()` ####
Assert the underlying response's status code is between **500** and **600**.

#### `Response::isSuccessful()` ####
Assert the underlying response's status code is between **200** and **300**.

#### `Response::__toString()` ####
Will return a string formatted representation of the underlying response object.
```
HTTP/1.1 200 OK
Content-Type: application/json;charset=utf-8

{"Hello": "World"}
```


## Decoratored ServerRequest Object Methods
The decorated `ServerRequestInterface` provides the following additional methods:

#### `ServerRequest::withAttributes($attributes)` ####
| Parameter       | Type      | Description                              |
|-----------------|-----------|------------------------------------------|
| **$attributes** | `array`   | Attributes to be appended to the request |

#### `ServerRequest::getContentCharset()` ####
Returns the detected charset from the `Content-Type` header of the underlying server request object. Returns `null` if no value is present.

#### `ServerRequest::getContentType()` ####
Returns the value from the `Content-Type` header of the underlying server request object. Returns `null` if no value is present.

#### `ServerRequest::getContentLength()` ####
Returns the value from the `Content-Length` header of the underlying server request object. Returns `null` if no value is present.

#### `ServerRequest::getCookieParam($key, $default)` ####
| Parameter     | Type     | Description                                            |
|---------------|----------|--------------------------------------------------------|
| **$key**      | `string` | The attribute name                                     |
| **$default**  | `mixed`  | Default value to return if the attribute does not exist |

#### `ServerRequest::getMediaType()` ####
Returns the first detected value from the `Content-Type` header of the underlying server request object. Returns `null` if no value is present.

#### `ServerRequest::getMediaTypeParams()` ####
Returns an array of detected values from the `Content-Type` header of the underlying server request object. Returns an empty array if no values are present.

#### `ServerRequest::getParam($key, $default)` ####
Returns the value from key in `$_POST` or `$_GET`

| Parameter    | Type     | Description                                             |
|--------------|----------|---------------------------------------------------------|
| **$key**     | `string` | The attribute name                                      |
| **$default** | `mixed`  | Default value to return if the attribute does not exist |

#### `ServerRequest::getParams()` ####
Returns a merged associative array of the `$_POST` and `$_GET` parameters.

#### `ServerRequest::getParsedBody()` ####
Returns the parsed body from the underlying server request object if it already has been parsed by the underlying PSR-7 implementation. If the parsed body is empty, our decorator attempts to detect the content type and parse the body using one of the registered media type parsers.

The default media type parsers support:
- JSON
- XML

You can register your own media type parser using the `ServerRequest::registerMediaTypeParser()` method.


#### `ServerRequest::getParsedBodyParam($key, $default)` ####
Returns the value from key in the parsed body of the underlying server request object.

| Parameter    | Type     | Description                                             |
|--------------|----------|---------------------------------------------------------|
| **$key**     | `string` | The attribute name                                      |
| **$default** | `mixed`  | Default value to return if the attribute does not exist |

#### `ServerRequest::getQueryParam($key, $default)` ####
Returns the value from key in the parsed `ServerRequest` query string

| Parameter     | Type     | Description                                             |
|---------------|----------|---------------------------------------------------------|
| **$key**      | `string` | The attribute name                                      |
| **$default**  | `mixed`  | Default value to return if the attribute does not exist |

#### `ServerRequest::getServerParam($key, $default)` ####
Returns the value from key in parsed server parameters from the underlying underlying server request object.

| Parameter    | Type     | Description                                              |
|--------------|----------|----------------------------------------------------------|
| **$key**     | `string` | The attribute name                                       |
| **$default** | `mixed`  | Default value to return if the attribute does not exist  |

#### `ServerRequest::registerMediaTypeParser($key, $default)` ####
Returns the value from key in parsed server parameters from the underlying server request object.

| Parameter      | Type       | Description                                            |
|----------------|------------|--------------------------------------------------------|
| **$mediaType** | `string`   | A HTTP media type (excluding content-type params)      |
| **$callable**  | `callable` | A callable that returns parsed contents for media type |

#### `ServerRequest::isMethod($method)` ####
| Parameter   | Type     | Description     |
|-------------|----------|-----------------|
| **$method** | `string` | The method name |

#### `ServerRequest::isDelete()` ####
Asserts that the underlying server request's method is `DELETE`

#### `ServerRequest::isGet()` ####
Asserts that the underlying server request's method is `GET`

#### `ServerRequest::isHead()` ####
Asserts that the underlying server request's method is `HEAD`

#### `ServerRequest::isOptions()` ####
Asserts that the underlying server request's method is `OPTIONS`

#### `ServerRequest::isPatch()` ####
Asserts that the underlying server request's method is `PATCH`

#### `ServerRequest::isPost()` ####
Asserts that the underlying server request's method is `POST`

#### `ServerRequest::isPut()` ####
Asserts that the underlying server request's method is `PUT`

#### `ServerRequest::isXhr()` ####
Asserts that the header `X-Requested-With` from the underlying server request is `XMLHttpRequest`

## Decorated Uri Object Methods
The decorated `UriInterface` provides the following additional methods:

#### `Uri::getBaseUrl()` ####
Returns the fully qualified base URL of the underlying uri object.

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security
If you discover security related issues, please email security@slimframework.com 
instead of using the issue tracker.

## Credits
- [Josh Lockhart](https://github.com/codeguy)
- [Andrew Smith](https://github.com/silentworks)
- [Rob Allen](https://github.com/akrabat)
- [Pierre Bérubé](https://github.com/l0gicgate)
- [All Contributors](../../contributors)

## License

This component is licensed under the MIT license. See [License File](LICENSE) 
for more information.
