<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Client\Exception;

use Ghostwriter\Http\Contract\Client\Exception\ClientExceptionInterface;
use RuntimeException;

final class ClientException extends RuntimeException implements ClientExceptionInterface
{
    public static function unableToConfigureCurlHandle(): self
    {
        return new self('Unable to configure CurlHandle');
    }

    public static function unableToCreateCurlHandle(): self
    {
        return new self('Unable to create CurlHandle');
    }

    public static function unableToCreateCurlMultiHandle(): self
    {
        return new self('Unable to create CurlMultiHandle');
    }
}
