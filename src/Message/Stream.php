<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message;

use Ghostwriter\Http\Contract\Message\StreamInterface;
use Ghostwriter\Http\Message\Exception\InvalidArgumentException;
use Ghostwriter\Http\Message\Exception\StreamIsInAnUnusableStateException;
use Ghostwriter\Http\Message\Exception\StreamIsNotReadableException;
use Ghostwriter\Http\Message\Exception\StreamIsNotSeekableException;
use Ghostwriter\Http\Message\Exception\StreamIsNotWritableException;
use Ghostwriter\Http\Message\Exception\UnableToReadFromStreamException;
use Ghostwriter\Http\Message\Exception\UnableToSeekInStreamException;
use Ghostwriter\Http\Message\Exception\UnableToWriteToStreamException;
use RuntimeException;
use const SEEK_CUR;
use const SEEK_END;
use const SEEK_SET;
use function array_key_exists;
use function fclose;
use function feof;
use function fopen;
use function fread;
use function fseek;
use function ftell;
use function fwrite;
use function get_resource_type;
use function is_resource;
use function is_string;
use function stream_get_contents;
use function stream_get_meta_data;

final class Stream implements StreamInterface
{
    public const FD_MEMORY = 'php://memory';

    public const FD_TEMP = 'php://temp';

    /**
     * SEEK_CUR - Set position to current location plus offset.
     *
     * @var int
     */
    public const SEEK_CUR = SEEK_CUR;

    /**
     * SEEK_END - Set position to end-of-file plus offset.
     *
     * @var int
     */
    public const SEEK_END = SEEK_END;

    /**
     * SEEK_SET - Set position equal to offset bytes.
     *
     * @var int
     */
    public const SEEK_SET = SEEK_SET;

    private bool $readable;

    /**
     * @var null|resource
     */
    private $resource;

    private bool $seekable;

    private bool $writable;

    /**
     * cursor position, contents.
     *
     * @param resource|StreamInterface|string $stream the stream resource cursor
     * @param string                          $mode   Mode with which to open stream
     */
    public function __construct(mixed $stream = self::FD_TEMP, string $mode = 'r+b')
    {
        /** @var false|resource $resource */
        $resource = match (true) {
            is_resource($stream) => $stream,
            is_string($stream) => fopen($stream, $mode),
            $stream instanceof StreamInterface => $stream->detach(),
        };

        $this->validateResource($resource);

        $meta = stream_get_meta_data($resource);
        $mode = $meta['mode'] ?? $mode;
        $this->seekable = $meta['seekable'] && 0 === fseek($resource, 0, self::SEEK_CUR);
        $this->writable = ! str_contains($mode, 'r') || str_contains($mode, '+');
        $this->readable = str_contains($mode, 'r') || str_contains($mode, '+');
        $this->resource = $resource;
    }

    public function __toString(): string
    {
        if (null === $this->resource || ! $this->readable) {
            return '';
        }

        try {
            if ($this->seekable) {
                $this->rewind();
            }

            return $this->getContents();
        } catch (RuntimeException) {
            return '';
        }
    }

    public function close(): void
    {
        if (null === $this->resource) {
            return;
        }

        $resource = $this->detach();
        if (null === $resource) {
            return;
        }

        fclose($resource);
    }

    /**
     * Creates a new Http Stream.
     *
     * @param resource|StreamInterface|string $resourceStreamOrString
     *
     * @throws InvalidArgumentException
     */
    public static function create(mixed $resourceStreamOrString): StreamInterface
    {
        return match (true) {
            is_resource($resourceStreamOrString) => new self($resourceStreamOrString),
            is_string($resourceStreamOrString) => self::fromString($resourceStreamOrString),
            $resourceStreamOrString instanceof StreamInterface => $resourceStreamOrString,
            default => throw InvalidArgumentException::invalidStreamCreateArgument()
        };
    }

    /**
     * @return null|resource
     */
    public function detach(): mixed
    {
        $stream = $this->resource;
        $this->resource = null;
        return $stream;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     */
    public function eof(): bool
    {
        if (null === $this->resource) {
            return true;
        }

        return feof($this->resource);
    }

    public static function fromString(string $string): self
    {
        $resource = fopen(self::FD_TEMP, 'w+b');
        if (false === $resource) {
            throw InvalidArgumentException::invalidStreamResourceUri(self::FD_TEMP);
        }

        fwrite($resource, $string);
        return new self($resource);
    }

    public function getContents(): string
    {
        $this->streamIsUsable();

        if (false === $this->readable) {
            throw new StreamIsNotReadableException();
        }

        $result = stream_get_contents($this->resource);
        if (false === $result) {
            throw new UnableToReadFromStreamException();
        }

        return $result;
    }

    public function getMetadata(?string $key = null): mixed
    {
        $this->streamIsUsable();

        $meta = stream_get_meta_data($this->resource);
        if (null === $key) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }

    public function getSize(): ?int
    {
        if (null === $this->resource) {
            return null;
        }

        $stats = fstat($this->resource);
        if (false === $stats) {
            return null;
        }

        return $stats['size'];
    }

    /**
     * Returns true if the stream is readable.
     */
    public function isReadable(): bool
    {
        return null !== $this->resource && $this->readable;
    }

    /**
     * Returns true if the stream is seekable.
     */
    public function isSeekable(): bool
    {
        return null !== $this->resource && $this->seekable;
    }

    /**
     * Returns true if the stream is writable.
     */
    public function isWritable(): bool
    {
        return null !== $this->resource && $this->writable;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the stream and return them. Fewer than $length bytes may be returned if underlying stream call returns fewer bytes.
     *
     * @throws RuntimeException if an error occurs
     *
     * @return string returns the data read from the stream, or an empty string if no bytes are available
     */
    public function read(int $length = self::READABLE_BYTES): string
    {
        $this->streamIsUsable();

        if (false === $this->readable) {
            throw new StreamIsNotReadableException();
        }

        $result = fread($this->resource, $length);
        if (false === $result) {
            throw new UnableToReadFromStreamException();
        }

        return $result;
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception; otherwise, it will perform a seek(0).
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * Seek to a position in the stream.
     */
    public function seek(int $offset, int $whence = self::SEEK_SET): void
    {
        $this->streamIsUsable();

        if (false === $this->seekable) {
            throw new StreamIsNotSeekableException();
        }

        if (-1 === fseek($this->resource, $offset, $whence)) {
            throw new UnableToSeekInStreamException();
        }
    }

    /**
     * Returns the current position of the file read/write pointer.
     *
     * @throws RuntimeException on error
     *
     * @return int Position of the file pointer
     */
    public function tell(): int
    {
        $this->streamIsUsable();

        $offset = ftell($this->resource);
        if (false === $offset) {
            throw new StreamIsInAnUnusableStateException();
        }

        return $offset;
    }

    /**
     * Write data to the stream and return the number of bytes written to the stream.
     *
     * @param string $string the string that is to be written
     *
     * @throws StreamIsNotWritableException
     * @throws UnableToWriteToStreamException
     */
    public function write(string $string): int
    {
        $this->streamIsUsable();

        if (false === $this->writable) {
            throw new StreamIsNotWritableException();
        }

        $result = fwrite($this->resource, $string);
        if (false === $result) {
            throw new UnableToWriteToStreamException();
        }

        return $result;
    }

    /**
     * @psalm-assert resource $this->resource
     *
     * @throws StreamIsInAnUnusableStateException
     */
    private function streamIsUsable(): void
    {
        if (null === $this->resource) {
            throw new StreamIsInAnUnusableStateException();
        }
    }

    /**
     * Determine if a resource is one of the resource types allowed to instantiate a Stream.
     *
     * @param false|resource $resource stream resource
     *
     * @psalm-assert resource $resource
     *
     * @throws InvalidArgumentException
     * @throws StreamIsInAnUnusableStateException
     */
    private function validateResource(mixed $resource): void
    {
        if (false === $resource) {
            throw new StreamIsInAnUnusableStateException();
        }

        if (array_key_exists(get_resource_type($resource), [
            'gd'=>0,
            'stream'=>0,
        ])) {
            return;
        }

        throw InvalidArgumentException::invalidStreamProvided();
    }
}
