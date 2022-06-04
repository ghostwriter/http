<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message;

use Ghostwriter\Http\Contract\Message\StreamInterface;
use InvalidArgumentException;
use const SEEK_CUR;
use function fopen;
use function fseek;
use function fwrite;
use function is_resource;
use function is_string;
use function stream_get_meta_data;

final class Stream implements StreamInterface
{
    private bool $readable = false;

    private bool $seekable = false;

    private ?int $size = null;

    /**
     * @var null|resource
     */
    private $stream;

    private ?string $uri = null;

    private bool $writable = false;

    public function __construct(
        private string $filename = 'php://temp',
        private string $mode = 'rb'
    ) {
    }

    public function __toString(): string
    {
        return $this->filename;
    }

    public function close(): void
    {
    }

    /**
     * Creates a new Http Stream.
     *
     * @param resource|StreamInterface|string $body
     *
     * @throws InvalidArgumentException
     */
    public static function create(mixed $body = ''): StreamInterface
    {
        if ($body instanceof StreamInterface) {
            return $body;
        }

        if (is_string($body)) {
            $resource = fopen('php://temp', 'rwb+');
            fwrite($resource, $body);
            $body = $resource;
        }

        if (is_resource($body)) {
            $new = new self();
            $new->stream = $body;
            $meta = stream_get_meta_data($new->stream);
            $new->seekable = $meta['seekable'] && 0 === fseek($new->stream, 0, SEEK_CUR);
//            $new->readable = isset(self::READ_WRITE_HASH['read'][$meta['mode']]);
//            $new->writable = isset(self::READ_WRITE_HASH['write'][$meta['mode']]);

            return $new;
        }

        throw new InvalidArgumentException(
            'First argument to Stream::create() must be a string, resource or StreamInterface.'
        );
    }

    public function detach(): mixed
    {
        return $this->stream;
    }

    public function eof(): bool
    {
        return false;
    }

    public function getContents(): string
    {
        return $this->mode;
    }

    public function getMetadata(?string $key = null): mixed
    {
        return $this->mode;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function read(int $length): string
    {
        return $this->mode;
    }

    public function rewind(): void
    {
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
    }

    public function tell(): int
    {
        return 0;
    }

    public function write(string $string): int
    {
        return 0;
    }
}
