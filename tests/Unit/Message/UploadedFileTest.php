<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Tests\Unit\Message;

use Ghostwriter\Http\Contract\Message\StreamInterface;
use Ghostwriter\Http\Contract\Message\UploadedFileInterface;
use Ghostwriter\Http\Message\Stream;
use Ghostwriter\Http\Message\UploadedFile;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Ghostwriter\Http\Message\UploadedFile
 *
 * @internal
 *
 * @small
 */
final class UploadedFileTest extends TestCase
{
    public const BLACK_LIVES_MATTER = '#BlackLivesMatter';

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::fromString
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::fromString
     * @covers \Ghostwriter\Http\Message\UploadedFile::__construct
     */
    public function testConstruct(): void
    {
        $stream = Stream::fromString(self::BLACK_LIVES_MATTER);
        $uploadedFile = new UploadedFile($stream);

        self::assertInstanceOf(UploadedFileInterface::class, $uploadedFile);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\UploadedFile::__construct
     * @covers \Ghostwriter\Http\Message\UploadedFile::getStream
     */
    public function testUploadedFile(): void
    {
        $stream = new Stream(Stream::FD_MEMORY, 'w+b');
        $uploadFile = new UploadedFile($stream, 0, UPLOAD_ERR_OK);

        self::assertSame($stream, $uploadFile->getStream());
    }

    public function testUploadedFile2(): void
    {
        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::fromString
     * @covers \Ghostwriter\Http\Message\Stream::getSize
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\UploadedFile::__construct
     * @covers \Ghostwriter\Http\Message\UploadedFile::getStream
     */
    public function testUploadedFileFromFile(): void
    {
        $stream = new Stream(tempnam(sys_get_temp_dir(), __FUNCTION__));
        $uploadFile = new UploadedFile($stream, $stream->getSize(), UPLOAD_ERR_OK);
        self::assertSame($stream, $uploadFile->getStream());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::__toString
     * @covers \Ghostwriter\Http\Message\Stream::fromString
     * @covers \Ghostwriter\Http\Message\Stream::getContents
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\UploadedFile::__construct
     * @covers \Ghostwriter\Http\Message\UploadedFile::getStream
     */
    public function testUploadedFileGetStreamRetrieveAStreamRepresentingTheUploadedFile(): void
    {
        $stream = Stream::fromString(self::BLACK_LIVES_MATTER);
        $uploadedFile = new UploadedFile($stream);

        self::assertInstanceOf(UploadedFileInterface::class, $uploadedFile);
        self::assertSame(self::BLACK_LIVES_MATTER, $stream->__toString());

        $uploadedStream = $uploadedFile->getStream();
        self::assertSame($stream, $uploadedStream);

        self::assertInstanceOf(StreamInterface::class, $uploadedStream);
        self::assertSame(self::BLACK_LIVES_MATTER, $uploadedStream->__toString());
    }

    public function testUploadedFileIsImmutable(): void
    {
        $this->expectNotToPerformAssertions();
        // Todo: figure out what mutable field can be mutated for testing. UploadedFile
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::create
     * @covers \Ghostwriter\Http\Message\Stream::eof
     * @covers \Ghostwriter\Http\Message\Stream::fromString
     * @covers \Ghostwriter\Http\Message\Stream::getMetadata
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::read
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::write
     * @covers \Ghostwriter\Http\Message\UploadedFile::__construct
     * @covers \Ghostwriter\Http\Message\UploadedFile::getStream
     * @covers \Ghostwriter\Http\Message\UploadedFile::moveTo
     */
    public function testUploadedFileMoveTo(): void
    {
        $stream = Stream::create(self::BLACK_LIVES_MATTER);
        $uploadFile = new UploadedFile($stream, 0, UPLOAD_ERR_OK);

        self::assertSame($stream, $uploadFile->getStream());
        $file = tempnam(sys_get_temp_dir(), __FUNCTION__);
        self::assertEmpty(file_get_contents($file));

        $uploadFile->moveTo($file);
        self::assertSame(self::BLACK_LIVES_MATTER, file_get_contents($file));
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::create
     * @covers \Ghostwriter\Http\Message\Stream::eof
     * @covers \Ghostwriter\Http\Message\Stream::getMetadata
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::read
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::write
     * @covers \Ghostwriter\Http\Message\UploadedFile::__construct
     * @covers \Ghostwriter\Http\Message\UploadedFile::getStream
     * @covers \Ghostwriter\Http\Message\UploadedFile::moveTo
     */
    public function testUploadedFileMoveToRemovesOriginalFile(): void
    {
        $content = '#BlackLivesMatter';
        $originalFile = tempnam(sys_get_temp_dir(), __FUNCTION__ . 'ORG');
        file_put_contents($originalFile, $content);

        $uploadFile = new UploadedFile(new Stream($originalFile), mb_strlen($content), UPLOAD_ERR_OK);

        $newFile = tempnam(sys_get_temp_dir(), __FUNCTION__ . 'NEW');
        self::assertEmpty(file_get_contents($newFile));

        self::assertFileExists($originalFile);
        $uploadFile->moveTo($newFile);
        self::assertFileExists($newFile);
        self::assertFileDoesNotExist($originalFile);
        self::assertSame($content, file_get_contents($newFile));
    }
    //
    //When the HTTP method is not POST.
    //When unit testing.
    //When operating under a non-SAPI environment,
}
