<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Contract\Factory;

use Ghostwriter\Http\Contract\Message\ServerRequestInterface;
use Ghostwriter\Http\Contract\Message\UriInterface;

interface ServerRequestFactoryInterface
{
    /**
     * Create a new server request.
     *
     * Note that server-params are taken precisely as given - no parsing/processing of the given values is performed,
     * and, in particular, no attempt is made to determine the HTTP method or URI, which must be provided explicitly.
     *
     * @param string              $method       the HTTP method associated with the request
     * @param string|UriInterface $uri          The URI associated with the request. If the value is a string,
     *                                          the factory MUST create a UriInterface instance based on it.
     * @param array               $serverParams array of SAPI parameters with which to seed
     *                                          the generated request instance
     *
     */
    public function createServerRequest(
        string $method,
        UriInterface|string $uri,
        array $serverParams = []
    ): ServerRequestInterface;
}
