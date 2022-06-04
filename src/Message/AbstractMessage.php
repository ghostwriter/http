<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message;

use Ghostwriter\Http\Contract\Message\MessageInterface;
use Ghostwriter\Http\Contract\Message\StreamInterface;
use InvalidArgumentException;

abstract class AbstractMessage implements MessageInterface
{
    protected array $headers;

    public function getBody(): StreamInterface
    {
        throw new InvalidArgumentException();
    }

    public function getHeader(string $name): array
    {
        return [];
    }

    public function getHeaderLine(string $name): string
    {
        return '';
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function getProtocolVersion(): string
    {
        return '';
    }

    public function hasHeader(string $name): bool
    {
        return true;
    }

    public function withAddedHeader(string $name, array|string $value): MessageInterface
    {
        return $this;
    }

    public function withBody(StreamInterface $stream): MessageInterface
    {
        return $this;
    }

    public function withHeader(string $name, array|string $value): MessageInterface
    {
        $clone = clone $this;
        $clone->headers[$name] = $value;

        return $clone;
    }

    public function withoutHeader(string $name): MessageInterface
    {
        $clone = clone $this;
        unset($clone->headers[$name]);

        return $clone;
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        return $this;
    }
}
