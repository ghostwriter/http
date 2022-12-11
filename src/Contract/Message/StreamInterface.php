<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Contract\Message;

use RuntimeException;
use Stringable;

/**
 * Describes a data stream.
 *
 * Typically, an instance will wrap a PHP stream; this interface provides a wrapper around the most common operations,
 * including serialization of the entire stream to a string.
 */
interface StreamInterface extends Stringable
{
    /**
     * @var int
     */
    public const MEGABYTE = 1_048_576;

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before reading data and read the stream until the
     * end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     *
     */
    public function __toString(): string;

    /**
     * Closes the stream and any underlying resources.
     */
    public function close(): void;

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return null|resource Underlying PHP stream, if any
     */
    public function detach();

    /**
     * Returns true if the stream is at the end of the stream.
     */
    public function eof(): bool;

    /**
     * Returns the remaining contents in a string.
     *
     * @throws RuntimeException if unable to read or an error occurs while reading
     */
    public function getContents(): string;

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     *
     * @param null|string $key specific metadata to retrieve
     *
     * @return null|array|mixed Returns an associative array if no key is provided.
     *                          Returns a specific key value if a key is provided and the value is found,
     *                          or null if the key is not found.
     *
     * #[ArrayShape([
     * "timed_out" => "bool",
     * "blocked" => "bool",
     * "eof" => "bool",
     * "unread_bytes" => "int",
     * "stream_type" => "string",
     * "wrapper_type" => "string",
     * "wrapper_data" => "mixed",
     * "mode" => "string",
     * "seekable" => "bool",
     * "uri" => "string",
     * "crypto" => "array",
     * "mediatype" => "string"
     * ])]
     */
    public function getMetadata(?string $key = null): mixed;

    /**
     * Get the size of the stream if known.
     *
     * @return null|int returns the size in bytes if known, or null if unknown
     */
    public function getSize(): ?int;

    /**
     * Returns whether the stream is readable.
     */
    public function isReadable(): bool;

    /**
     * Returns whether the stream is seekable.
     */
    public function isSeekable(): bool;

    /**
     * Returns whether the stream is writable.
     */
    public function isWritable(): bool;

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return them.
     *                    Fewer than $length bytes may be returned if underlying stream
     *                    call returns fewer bytes.
     *
     * @throws RuntimeException if an error occurs
     *
     * @return string returns the data read from the stream, or an empty string
     *                if no bytes are available
     */
    public function read(int $length): string;

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception; otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     *
     * @throws RuntimeException on failure
     */
    public function rewind(): void;

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     *
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *                    based on the seek offset. Valid values are identical to the built-in
     *                    PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *                    offset bytes SEEK_CUR: Set position to current location plus offset
     *                    SEEK_END: Set position to end-of-stream plus offset.
     *
     * @throws RuntimeException on failure
     */
    public function seek(int $offset, int $whence = SEEK_SET): void;

    /**
     * Returns the current position of the file read/write pointer.
     *
     * @throws RuntimeException on error
     *
     * @return int Position of the file pointer
     */
    public function tell(): int;

    /**
     * Write data to the stream.
     *
     * @param string $string the string that is to be written
     *
     * @throws RuntimeException on failure
     *
     * @return int returns the number of bytes written to the stream
     */
    public function write(string $string): int;
}
