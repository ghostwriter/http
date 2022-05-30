<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Factory;

use Ghostwriter\Http\Contract\Factory\ServerRequestFactoryInterface;
use Ghostwriter\Http\Contract\Message\ServerRequestInterface;
use Ghostwriter\Http\Contract\Message\UriInterface;
use RuntimeException;

final class ServerRequestFactory implements ServerRequestFactoryInterface
{
    public function createServerRequest(
        string $method,
        string|UriInterface $uri,
        array $serverParams = []
    ): ServerRequestInterface {
        throw new RuntimeException();
    }
}
