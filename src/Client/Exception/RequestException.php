<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Client\Exception;

use Ghostwriter\Http\Contract\Client\Exception\ClientExceptionInterface;
use Ghostwriter\Http\Contract\Client\Exception\NetworkExceptionInterface;
use Ghostwriter\Http\Contract\Client\Exception\RequestExceptionInterface;
use Ghostwriter\Http\Contract\Message\RequestInterface;
use RuntimeException;
use Throwable;

final class RequestException extends RuntimeException implements RequestExceptionInterface
{

    public function __construct(private RequestInterface $request, string $message)
    {
        parent::__construct($message);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
