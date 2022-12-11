<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Factory;

use Ghostwriter\Http\Contract\Factory\ResponseFactoryInterface;
use Ghostwriter\Http\Contract\Message\ResponseInterface;
use Ghostwriter\Http\Message\Response;

final class ResponseFactory implements ResponseFactoryInterface
{
    public function createResponse(
        int $code = self::HTTP_200_OK,
        string $reasonPhrase = self::HTTP_REASON_PHRASE[self::HTTP_200_OK]
    ): ResponseInterface {
        return (new Response())->withStatus($code, $reasonPhrase);
    }
}
