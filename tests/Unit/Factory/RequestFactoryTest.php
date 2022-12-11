<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Tests\Unit\Factory;

use Ghostwriter\Http\Contract\Factory\RequestFactoryInterface;
use Ghostwriter\Http\Contract\Message\RequestInterface;
use Ghostwriter\Http\Factory\RequestFactory;
use Ghostwriter\Http\Message\Request;
use Ghostwriter\Http\Message\Uri;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Ghostwriter\Http\Factory\RequestFactory
 *
 * @internal
 *
 * @small
 */
final class RequestFactoryTest extends TestCase
{
    /**
     * @covers \Ghostwriter\Http\Factory\RequestFactory::createRequest
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(RequestFactory::class, $this->createRequestFactory());
        self::assertInstanceOf(RequestFactoryInterface::class, $this->createRequestFactory());
    }

    /**
     * @covers \Ghostwriter\Http\Factory\RequestFactory::createRequest
     * @covers \Ghostwriter\Http\Factory\UriFactory::createUri
     * @covers \Ghostwriter\Http\Message\Request::__construct
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::create
     * @covers \Ghostwriter\Http\Message\Traits\RequestTrait::getUri
     * @covers \Ghostwriter\Http\Message\Uri::__construct
     * @covers \Ghostwriter\Http\Message\Uri::__toString
     * @covers \Ghostwriter\Http\Message\Uri::normalize
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeHost
     * @covers \Ghostwriter\Http\Message\Uri::sanitizeScheme
     * @covers \Ghostwriter\Http\Message\Stream::fromString
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     */
    public function testCreateUri(): void
    {
        $uri = new Uri('https://example.com');
        $requestFactory = $this->createRequestFactory();
        $request = $requestFactory->createRequest(Request::METHOD_GET, $uri);

        self::assertInstanceOf(RequestFactoryInterface::class, $requestFactory);
        self::assertInstanceOf(RequestInterface::class, $request);
        self::assertSame($uri, $request->getUri());
    }

    private function createRequestFactory(): RequestFactory
    {
        return new RequestFactory();
    }
}
