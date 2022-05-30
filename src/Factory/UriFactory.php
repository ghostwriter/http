<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Factory;

use Ghostwriter\Http\Contract\Factory\UriFactoryInterface;
use Ghostwriter\Http\Contract\Factory\UriInterface;
use InvalidArgumentException;

final class UriFactory implements UriFactoryInterface
{
    public function createUri(string $uri = ''): UriInterface
    {
        throw new InvalidArgumentException();
    }
}
