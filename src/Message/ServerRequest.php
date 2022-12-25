<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message;

use Ghostwriter\Http\Contract\Message\ServerRequestInterface;
use Ghostwriter\Http\Contract\Message\UriInterface;
use Ghostwriter\Http\Message\Traits\RequestTrait;

final class ServerRequest implements ServerRequestInterface
{
    use RequestTrait;

    //    protected UriInterface $uri;
    public function __construct(
        //        protected
        string $method,
        string|UriInterface $uri,
        protected array $serverParams = []
    ) {
        $this->uri = $uri instanceof UriInterface ? $uri : new Uri($uri);
        $this->method = $method;
    }

    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $default ?? $name;
    }

    public function getAttributes(): array
    {
        return [];
    }

    public function getCookieParams(): array
    {
        return [];
    }

    public function getParsedBody(): object|array|null
    {
        return $this;
    }

    public function getQueryParams(): array
    {
        return [];
    }

    public function getServerParams(): array
    {
        return [];
    }

    public function getUploadedFiles(): array
    {
        return [];
    }

    public function withAttribute(string $name, mixed $value): ServerRequestInterface
    {
        return $this;
    }

    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        return $this;
    }

    public function withoutAttribute(string $name): ServerRequestInterface
    {
        return $this;
    }

    public function withParsedBody(object|array|null $data): ServerRequestInterface
    {
        return $this;
    }

    public function withQueryParams(array $query): ServerRequestInterface
    {
        return $this;
    }

    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        return $this;
    }
}
