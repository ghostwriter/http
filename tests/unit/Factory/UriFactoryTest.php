<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Tests\Unit\Factory;

use Ghostwriter\Http\Contract\Factory\UriFactoryInterface;
use Ghostwriter\Http\Contract\Message\UriInterface;
use Ghostwriter\Http\Factory\UriFactory;
use Ghostwriter\Http\Message\Uri;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Ghostwriter\Http\Factory\UriFactory
 *
 * @internal
 *
 * @small
 */
final class UriFactoryTest extends TestCase
{
    /**
     * @covers \Ghostwriter\Http\Factory\UriFactory::createUri
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::encode
     * @covers \Ghostwriter\Http\Message\Uri::getAuthority
     * @covers \Ghostwriter\Http\Message\Uri::getFragment
     * @covers \Ghostwriter\Http\Message\Uri::getHost
     * @covers \Ghostwriter\Http\Message\Uri::getPath
     * @covers \Ghostwriter\Http\Message\Uri::getPort
     * @covers \Ghostwriter\Http\Message\Uri::getQuery
     * @covers \Ghostwriter\Http\Message\Uri::getScheme
     * @covers \Ghostwriter\Http\Message\Uri::getUserInfo
     * @covers \Ghostwriter\Http\Message\Uri::isNonStandardPort
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeFragment
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeInvalidUTF8Characters
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePath
     * @covers \Ghostwriter\Http\Message\Uri::sanitizePort
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQuery
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeQueryStringKeyOrValue
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeUserInfo
     */
    public function testCreateUri(): void
    {
        $url = 'https://user:pass@example.com:8080/path/?query=string#fragment';

        $uriFactory = $this->createUriFactory();
        self::assertInstanceOf(UriFactory::class, $uriFactory);
        self::assertInstanceOf(UriFactoryInterface::class, $uriFactory);

        $uri = $uriFactory->createUri($url);
        self::assertInstanceOf(Uri::class, $uri);
        self::assertInstanceOf(UriInterface::class, $uri);

        self::assertSame('https', $uri->getScheme());
        self::assertSame('user:pass@example.com:8080', $uri->getAuthority());
        self::assertSame('user:pass', $uri->getUserInfo());
        self::assertSame('example.com', $uri->getHost());
        self::assertSame(8080, $uri->getPort());
        self::assertSame('/path/', $uri->getPath());
        self::assertSame('query=string', $uri->getQuery());
        self::assertSame('fragment', $uri->getFragment());
        self::assertSame($url, $uri->__toString());
    }

    private function createUriFactory(): UriFactory
    {
        return new UriFactory();
    }
}
