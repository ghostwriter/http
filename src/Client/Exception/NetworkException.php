<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Client\Exception;

use Ghostwriter\Http\Contract\Client\Exception\NetworkExceptionInterface;
use Ghostwriter\Http\Contract\Message\RequestInterface;
use RuntimeException;
use Throwable;

final class NetworkException extends RuntimeException implements NetworkExceptionInterface
{
    public function __construct(
        private RequestInterface $request,
        string $message,
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
