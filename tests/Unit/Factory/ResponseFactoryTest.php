<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Tests\Unit\Factory;

use Ghostwriter\Http\Contract\Factory\ResponseFactoryInterface;
use Ghostwriter\Http\Contract\Message\StatusCodeInterface;
use Ghostwriter\Http\Factory\ResponseFactory;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Ghostwriter\Http\Factory\ResponseFactory
 *
 * @internal
 *
 * @small
 */
final class ResponseFactoryTest extends TestCase
{
    /**
     * @covers \Ghostwriter\Http\Factory\ResponseFactory::createResponse
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(ResponseFactoryInterface::class, $this->createResponseFactory());
    }

    /**
     * @covers \Ghostwriter\Http\Factory\ResponseFactory::createResponse
     * @covers \Ghostwriter\Http\Message\Response::__construct
     * @covers \Ghostwriter\Http\Message\Response::getReasonPhrase
     * @covers \Ghostwriter\Http\Message\Response::getStatusCode
     * @covers \Ghostwriter\Http\Message\Response::withStatus
     */
    public function testCreateResponse(): void
    {
        $responseFactory = $this->createResponseFactory();
        $response = $responseFactory->createResponse();

        self::assertInstanceOf(ResponseFactoryInterface::class, $responseFactory);
        self::assertSame(StatusCodeInterface::HTTP_200_OK, $response->getStatusCode());
        self::assertSame(
            StatusCodeInterface::HTTP_REASON_PHRASE[StatusCodeInterface::HTTP_200_OK],
            $response->getReasonPhrase()
        );

        $response = $responseFactory->createResponse(StatusCodeInterface::HTTP_429_TOO_MANY_REQUESTS, __FUNCTION__);
        self::assertInstanceOf(ResponseFactoryInterface::class, $responseFactory);
        self::assertSame(StatusCodeInterface::HTTP_429_TOO_MANY_REQUESTS, $response->getStatusCode());
        self::assertSame(__FUNCTION__, $response->getReasonPhrase());
    }

    private function createResponseFactory(): ResponseFactory
    {
        return new ResponseFactory();
    }
}
