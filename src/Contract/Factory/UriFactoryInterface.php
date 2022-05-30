<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Contract\Factory;

use InvalidArgumentException;

interface UriFactoryInterface
{
    /**
     * Create a new URI.
     *
     * @throws InvalidArgumentException if the given URI cannot be parsed
     */
    public function createUri(string $uri = ''): UriInterface;
}
