<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message\Exception;

use Ghostwriter\Http\Contract\Message\Exception\MessageExceptionInterface;
use RuntimeException;

final class StreamIsNotSeekableException extends RuntimeException implements MessageExceptionInterface
{
}