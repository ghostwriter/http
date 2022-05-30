<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Factory;

use Ghostwriter\Http\Contract\Factory\ResponseFactoryInterface;
use Ghostwriter\Http\Contract\Message\ResponseInterface;
use RuntimeException;

final class ResponseFactory implements ResponseFactoryInterface
{
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        throw new RuntimeException();
    }
}
