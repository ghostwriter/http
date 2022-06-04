<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Server;

use Ghostwriter\Http\Contract\Message\ResponseInterface;
use Ghostwriter\Http\Contract\Message\ServerRequestInterface;
use Ghostwriter\Http\Contract\Server\RequestHandlerInterface;

abstract class AbstractRequestHandler implements RequestHandlerInterface
{
    abstract public function handle(ServerRequestInterface $serverRequest): ResponseInterface;
}
