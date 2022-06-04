<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message;

use Ghostwriter\Http\Contract\Message\RequestInterface;
use Ghostwriter\Http\Contract\Message\UriInterface;

abstract class AbstractRequest extends AbstractMessage implements RequestInterface
{
    protected string $method;

    protected mixed $requestTarget;

    protected UriInterface $uri;

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getRequestTarget(): string
    {
        return $this->requestTarget;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withMethod(string $method): RequestInterface
    {
        $clone = clone $this;
        $clone->method = $method;

        return $clone;
    }

    public function withRequestTarget(mixed $requestTarget): RequestInterface
    {
        $clone = clone $this;
        $clone->requestTarget = $requestTarget;

        return $clone;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        $clone = clone $this;
        $clone->uri = $uri;

        return $clone;
    }
}
