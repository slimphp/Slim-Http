<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Http;

use InvalidArgumentException;
use Slim\Http\Factory\DecoratedUriFactory;
use Slim\Tests\Http\Providers\Psr17FactoryProvider;

class UriTest extends TestCase
{
    public function testDisableSetter()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com');
            $uri->foo = 'bar';

            $this->assertFalse(property_exists($uri, 'foo'));
        }
    }

    public function testGetScheme()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com');
            $this->assertEquals('https', $uri->getScheme());
        }
    }

    public function testWithScheme()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com');
            $uri = $uri->withScheme('http');

            $this->assertEquals('http', $uri->getScheme());
        }
    }

    public function testWithSchemeEmpty()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com');
            $uri = $uri->withScheme('');

            $this->assertEquals('', $uri->getScheme());
        }
    }

    public function testGetAuthorityWithUsernameAndPassword()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com');
            $uri = $uri->withUserInfo('user', 'password');

            $this->assertEquals('user:password@google.com', $uri->getAuthority());
        }
    }

    public function testGetAuthorityWithUsername()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://user@google.com');
            $uri = $uri->withUserInfo('user');

            $this->assertEquals('user@google.com', $uri->getAuthority());
        }
    }

    public function testGetAuthority()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://user@google.com');
            $this->assertEquals('user@google.com', $uri->getAuthority());
        }
    }

    public function testGetAuthorityWithNonStandardPort()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com:400');
            $this->assertEquals('google.com:400', $uri->getAuthority());
        }
    }

    public function testGetUserInfoWithUsernameAndPassword()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com');
            $uri = $uri->withUserInfo('user', 'password');

            $this->assertEquals('user:password', $uri->getUserInfo());
        }
    }

    public function testGetUserInfoWithUsernameAndPasswordEncodesCorrectly()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://user%40pass%3Aword@google.com');
            $this->assertEquals('user%40pass%3Aword', $uri->getUserInfo());
        }
    }

    public function testGetUserInfoWithUsername()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://user@google.com');
            $this->assertEquals('user', $uri->getUserInfo());
        }
    }

    public function testGetUserInfoNone()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com');
            $this->assertEquals('', $uri->getUserInfo());
        }
    }

    public function testGetHost()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com');
            $this->assertEquals('google.com', $uri->getHost());
        }
    }

    public function testWithHost()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com');
            $uri = $uri->withHost('microsoft.com');

            $this->assertEquals('microsoft.com', $uri->getHost());
        }
    }

    public function testWithPort()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com');
            $uri = $uri->withPort(400);

            $this->assertEquals(400, $uri->getPort());
        }
    }

    public function testWithPortNull()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com');
            $uri = $uri->withPort(null);

            $this->assertEquals(null, $uri->getPort());
        }
    }

    public function testWithPortInvalidInt()
    {
        $this->expectException(InvalidArgumentException::class);

        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com');
            $uri->withPort(70000);
        }
    }

    public function testWithPortInvalidString()
    {
        $this->expectException(InvalidArgumentException::class);

        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com');
            $uri->withPort('invalid');
        }
    }

    public function testGetPath()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com/path');
            $this->assertEquals('/path', $uri->getPath());
        }
    }

    public function testWithPath()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com/path');
            $uri = $uri->withPath('/newPath');

            $this->assertEquals('/newPath', $uri->getPath());
        }
    }

    public function testWithPathWithoutPrefix()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com/path');
            $uri = $uri->withPath('newPath');

            $this->assertEquals('newPath', $uri->getPath());
        }
    }

    public function testWithPathEmptyValue()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com/path');
            $uri = $uri->withPath('');

            $this->assertEquals('', $uri->getPath());
        }
    }

    public function testGetQuery()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com/path?foo=bar');
            $this->assertEquals('foo=bar', $uri->getQuery());
        }
    }

    public function testWithQuery()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com/path?foo=bar');
            $uri = $uri->withQuery('bar=baz');

            $this->assertEquals('bar=baz', $uri->getQuery());
        }
    }

    public function testWithQueryEmpty()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com/path?foo=bar');
            $uri = $uri->withQuery('');

            $this->assertEquals('', $uri->getQuery());
        }
    }

    public function testGetFragment()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com/#fragment');
            $this->assertEquals('fragment', $uri->getFragment());
        }
    }

    public function testWithFragment()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com/#fragment');
            $uri = $uri->withFragment('new-fragment');

            $this->assertEquals('new-fragment', $uri->getFragment());
        }
    }

    public function testWithFragmentEmpty()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com/#fragment');
            $uri = $uri->withFragment('');

            $this->assertEquals('', $uri->getFragment());
        }
    }

    public function testToString()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://user@google.com/path/otherpath?foo=bar#fragment');
            $this->assertEquals('https://user@google.com/path/otherpath?foo=bar#fragment', (string) $uri);

            $uri = $uri->withPath('new-path');
            $this->assertEquals('https://user@google.com/new-path?foo=bar#fragment', (string) $uri);

            $uri = $uri->withPath('/other-path');
            $this->assertEquals('https://user@google.com/other-path?foo=bar#fragment', (string) $uri);
        }
    }

    public function testGetBaseUrl()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com/path/otherpath?foo=bar#fragment');
            $this->assertEquals('https://google.com', $uri->getBaseUrl());
        }
    }

    public function testGetBaseUrlWithNoBasePath()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://google.com/index.php');
            $this->assertEquals('https://google.com', $uri->getBaseUrl());
        }
    }

    public function testGetBaseUrlWithAuthority()
    {
        foreach ($this->factoryProviders as $factoryProvider) {
            /** @var Psr17FactoryProvider $provider */
            $provider = new $factoryProvider();
            $decoratedUriFactory = new DecoratedUriFactory($provider->getUriFactory());

            $uri = $decoratedUriFactory->createUri('https://user:password@google.com/path/otherpath?foo=bar#fragment');
            $this->assertEquals('https://user:password@google.com', $uri->getBaseUrl());
        }
    }
}
