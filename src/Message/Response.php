<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message;

use Ghostwriter\Http\Contract\Message\ResponseInterface;
use Ghostwriter\Http\Message\Traits\ResponseTrait;

final class Response implements ResponseInterface
{
    use ResponseTrait;

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
