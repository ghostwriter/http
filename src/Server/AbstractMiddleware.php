<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Server;

use Ghostwriter\Http\Contract\Message\ResponseInterface;
use Ghostwriter\Http\Contract\Message\ServerRequestInterface;
use Ghostwriter\Http\Contract\Server\MiddlewareInterface;
use Ghostwriter\Http\Contract\Server\RequestHandlerInterface;

abstract class AbstractMiddleware implements MiddlewareInterface
{
    abstract public function process(
        ServerRequestInterface $serverRequest,
        RequestHandlerInterface $requestHandler
    ): ResponseInterface;
}
