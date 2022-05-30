<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Client\Exception;

use Ghostwriter\Http\Contract\Client\Exception\NetworkExceptionInterface;
use Ghostwriter\Http\Contract\Message\RequestInterface;
use RuntimeException;

final class NetworkException extends RuntimeException implements NetworkExceptionInterface
{
    public function __construct(
        private RequestInterface $request,
        string $message
    ) {
        parent::__construct($message);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
