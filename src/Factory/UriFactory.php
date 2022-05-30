<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Factory;

use Ghostwriter\Http\Contract\Factory\UriFactoryInterface;
use Ghostwriter\Http\Contract\Message\UriInterface;
use Ghostwriter\Http\Message\Uri;

final class UriFactory implements UriFactoryInterface
{
    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($uri);
    }
}
