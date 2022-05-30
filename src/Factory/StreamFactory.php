<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Factory;

use Ghostwriter\Http\Contract\Factory\StreamFactoryInterface;
use Ghostwriter\Http\Contract\Message\StreamInterface;
use InvalidArgumentException;
use RuntimeException;

final class StreamFactory implements StreamFactoryInterface
{
    public function createStream(string $content = ''): StreamInterface
    {
        throw new RuntimeException();
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        throw new InvalidArgumentException();
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        throw new InvalidArgumentException();
    }
}
