<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Contract\Factory;

use Ghostwriter\Http\Contract\Message\RequestInterface;
use Ghostwriter\Http\Contract\Message\UriInterface;

interface RequestFactoryInterface
{
    /**
     * Create a new request.
     *
     * @param string              $method The HTTP method associated with the request
     * @param string|UriInterface $uri    The URI associated with the request. If the value is a string,
     *                                    the factory MUST create a UriInterface instance based on it.
     *
     */
    public function createRequest(string $method, UriInterface|string $uri): RequestInterface;
}
