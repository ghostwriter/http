<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Factory;

use Ghostwriter\Http\Contract\Factory\RequestFactoryInterface;
use Ghostwriter\Http\Contract\Message\RequestInterface;
use Ghostwriter\Http\Contract\Message\UriInterface;
use Ghostwriter\Http\Message\Request;

final class RequestFactory implements RequestFactoryInterface
{
    public function createRequest(string $method, UriInterface|string $uri): RequestInterface
    {
        return new Request($method, $uri);
    }
}
