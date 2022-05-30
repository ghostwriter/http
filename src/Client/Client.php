<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Client;

use CurlHandle;
use Ghostwriter\Http\Client\Exception\ClientException;
use Ghostwriter\Http\Contract\Client\ClientInterface;
use Ghostwriter\Http\Contract\Factory\ResponseFactoryInterface;
use Ghostwriter\Http\Contract\Message\RequestInterface;
use Ghostwriter\Http\Contract\Message\ResponseInterface;

final class Client implements ClientInterface
{
    private ?CurlHandle $curlHandle = null;

    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private array $curlOptions = []
    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        if ([] === $this->curlOptions) {
            throw new ClientException();
        }
        return $this->responseFactory->createResponse();
    }
}
