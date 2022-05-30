<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Contract\Client\Exception;

use Ghostwriter\Http\Contract\Message\RequestInterface;

/**
 * Thrown when the request cannot be completed because of network issues.
 *
 * There is no response object as this exception is thrown when no response has been received.
 *
 * Example: the target host name can not be resolved or the connection failed.
 */
interface NetworkExceptionInterface extends ClientExceptionInterface
{
    /**
     * Returns the Request.
     *
     * The request object MAY be a different object from the one passed to ClientInterface::sendRequest()
     */
    public function getRequest(): RequestInterface;
}
