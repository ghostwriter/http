<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Contract\Client;

use Ghostwriter\Http\Contract\Client\Exception\ClientExceptionInterface;
use Ghostwriter\Http\Contract\Message\RequestInterface;
use Ghostwriter\Http\Contract\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * Sends a Request and returns a Response.
     *
     * @throws ClientExceptionInterface if an error happens while processing the request
     */
    public function sendRequest(RequestInterface $request): ResponseInterface;
}
