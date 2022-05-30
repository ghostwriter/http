<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Contract\Exception;

use Psr\Http\Client\ClientExceptionInterface as PsrClientExceptionInterface;

/**
 * Every HTTP client related exception MUST implement this interface.
 */
interface ClientExceptionInterface extends PsrClientExceptionInterface
{
}
