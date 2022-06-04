<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Client\Exception;

use Ghostwriter\Http\Contract\Client\Exception\ClientExceptionInterface;
use RuntimeException;

final class ClientException extends RuntimeException implements ClientExceptionInterface
{
}
