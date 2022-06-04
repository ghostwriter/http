<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Message;

use Ghostwriter\Http\Contract\Message\StreamInterface;
use Ghostwriter\Http\Contract\Message\UploadedFileInterface;
use InvalidArgumentException;
use RuntimeException;
use const UPLOAD_ERR_OK;
use function error_get_last;
use function fopen;
use function in_array;
use function sprintf;

final class UploadedFile implements UploadedFileInterface
{
    public function __construct(
        private StreamInterface $stream,
        private ?int $size = null,
        private int $error = UPLOAD_ERR_OK,
        private ?string $clientFilename = null,
        private ?string $clientMediaType = null
    ) {
        if (! in_array($error, self::UPLOAD_ERROR_CODES, true)) {
            throw new InvalidArgumentException(
                'Invalid error status for UploadedFile; must be an UPLOAD_ERR_* constant'
            );
        }

        if (UPLOAD_ERR_OK !== $error) {
            throw new InvalidArgumentException('Invalid stream provided for UploadedFile');
        }
    }

    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getStream(): StreamInterface
    {
        if (UPLOAD_ERR_OK !== $this->error) {
            throw new RuntimeException('Cannot retrieve stream provided for UploadedFile due to upload error.');
        }

        return $this->stream;
    }

    public function moveTo(string $targetPath): void
    {
        $stream = $this->getStream();
        if ($stream->isSeekable()) {
            $stream->seek(0);
        }

        $resource = @fopen($targetPath, 'wb');
        if (false === $resource) {
            throw new RuntimeException(sprintf(
                'The file "%s" cannot be opened: %s',
                $targetPath,
                error_get_last()['message'] ?? ''
            ));
        }

        $destination = Stream::create($resource);

        while (! $stream->eof()) {
            if (0 === $destination->write($stream->read(StreamInterface::MEGABYTE))) {
                break;
            }
        }
    }
}
