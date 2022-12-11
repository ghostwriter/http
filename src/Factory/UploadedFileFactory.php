<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Factory;

use Ghostwriter\Http\Contract\Factory\UploadedFileFactoryInterface;
use Ghostwriter\Http\Contract\Message\StreamInterface;
use Ghostwriter\Http\Contract\Message\UploadedFileInterface;
use Ghostwriter\Http\Message\UploadedFile;
use const UPLOAD_ERR_OK;

final class UploadedFileFactory implements UploadedFileFactoryInterface
{
    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface {
        return new UploadedFile($stream, $stream->getSize() ?? $size, $error, $clientFilename, $clientMediaType);
    }
}
