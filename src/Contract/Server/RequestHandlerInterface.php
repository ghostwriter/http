<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Contract\Server;

use Ghostwriter\Http\Contract\Message\ResponseInterface;
use Ghostwriter\Http\Contract\Message\ServerRequestInterface;

/**
 * Handles a server request and produces a response.
 *
 * An HTTP request handler process an HTTP request in order to produce an HTTP response.
 */
interface RequestHandlerInterface
{
    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $serverRequest): ResponseInterface;
}
