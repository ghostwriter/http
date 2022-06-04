<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message;

use Ghostwriter\Http\Contract\Message\ResponseInterface;

final class Response extends AbstractMessage implements ResponseInterface
{
    private int $code;

    private string $reasonPhrase;

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function getStatusCode(): int
    {
        return $this->code;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        $this->code = $code;
        $this->reasonPhrase = $reasonPhrase;

        return $this;
    }
}
