<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Server;

use Ghostwriter\Http\Contract\Message\ResponseInterface;
use Ghostwriter\Http\Contract\Message\ServerRequestInterface;
use Ghostwriter\Http\Contract\Server\RequestHandlerInterface;

final class Middleware extends AbstractMiddleware
{
    public function process(
        ServerRequestInterface $serverRequest,
        RequestHandlerInterface $requestHandler
    ): ResponseInterface {
        return $requestHandler->handle($serverRequest);
    }
}
