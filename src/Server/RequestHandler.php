<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Server;

use Ghostwriter\Http\Contract\Factory\ResponseFactoryInterface;
use Ghostwriter\Http\Contract\Message\ResponseInterface;
use Ghostwriter\Http\Contract\Message\ServerRequestInterface;

final class RequestHandler extends AbstractRequestHandler
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory
    ) {}

    public function handle(ServerRequestInterface $serverRequest): ResponseInterface
    {
        return $this->responseFactory->createResponse();
    }
}
