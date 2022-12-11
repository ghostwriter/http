<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Factory;

use Ghostwriter\Http\Contract\Factory\StreamFactoryInterface;
use Ghostwriter\Http\Contract\Message\StreamInterface;
use Ghostwriter\Http\Message\Stream;

final class StreamFactory implements StreamFactoryInterface
{
    public function createStream(string $content = ''): StreamInterface
    {
        return Stream::fromString($content);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r+b'): StreamInterface
    {
        return new Stream($filename, $mode);
    }

    /**
     * @param resource|StreamInterface $resource
     */
    public function createStreamFromResource(mixed $resource): StreamInterface
    {
        return Stream::create($resource);
    }
}
