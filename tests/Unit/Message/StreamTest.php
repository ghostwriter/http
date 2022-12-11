<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Tests\Unit\Message;

use Ghostwriter\Http\Message\Exception\StreamIsInAnUnusableStateException;
use Ghostwriter\Http\Message\Exception\StreamIsNotSeekableException;
use Ghostwriter\Http\Message\Exception\UnableToSeekInStreamException;
use Ghostwriter\Http\Message\Stream;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass \Ghostwriter\Http\Message\Stream
 *
 * @internal
 *
 * @small
 *
 * @psalm-suppress MissingConstructor
 */
final class StreamTest extends TestCase
{
    /**
     * @var string
     */
    private const BLACK_LIVES_MATTER = '#BlackLivesMatter';

    /**
     * @var string
     */
    private const IMG_URL = 'https://github.com/ghostwriter.png';

    /**
     * @param resource|string $data
     */
    public function createStream(mixed $data): Stream
    {
        return new Stream($data);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::close
     * @covers \Ghostwriter\Http\Message\Stream::detach
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     */
    public function testClose(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLACK_LIVES_MATTER);
        $stream = $this->createStream($resource);

        self::assertIsResource($resource);
        $stream->close();
        self::assertIsClosedResource($resource);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::detach
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     */
    public function testDetach(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, 'abc');
        $stream = $this->createStream($resource);

        self::assertSame($resource, $stream->detach());
        self::assertNull($stream->detach());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::eof
     * @covers \Ghostwriter\Http\Message\Stream::isReadable
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::read
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     * @covers \Ghostwriter\Http\Message\Stream::tell
     */
    public function testEofReturnsTrueIfTheStreamIsAtTheEndOfTheStream(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLACK_LIVES_MATTER);
        $stream = $this->createStream($resource);

        self::assertSame(17, mb_strlen(self::BLACK_LIVES_MATTER));
        $stream->seek(0);
        self::assertFalse($stream->eof());

        $stream->read(20);
        $stream->read(10);
        self::assertTrue($stream->eof());
        self::assertSame(17, $stream->tell());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::getContents
     * @covers \Ghostwriter\Http\Message\Stream::isReadable
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     * @covers \Ghostwriter\Http\Message\Stream::write
     */
    public function testGetContents(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLACK_LIVES_MATTER);

        $stream = $this->createStream($resource);
        $stream->rewind();

        $stream->seek(6);
        self::assertSame('LivesMatter', $stream->getContents());
        self::assertSame('', $stream->getContents());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::getSize
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     */
    public function testGetSize(): void
    {
        $resource = fopen('php://memory', 'rwb');

        fwrite($resource, 'abc');

        $stream = $this->createStream($resource);

        self::assertSame(3, $stream->getSize());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::isReadable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     */
    public function testIsNotReadable(): void
    {
        $resource = fopen(self::IMG_URL, 'rb');
        $stream = $this->createStream($resource);
        self::assertTrue($stream->isReadable());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     */
    public function testIsNotSeekable(): void
    {
        $resource = fopen(self::IMG_URL, 'rb');
        $stream = $this->createStream($resource);
        self::assertFalse($stream->isSeekable());
    }

    /**
     * @group internet
     *
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::isWritable
     */
    public function testIsNotWritable(): void
    {
        $resource = fopen(self::IMG_URL, 'rb');
        $stream = $this->createStream($resource);
        self::assertFalse($stream->isWritable());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::isReadable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     */
    public function testIsReadable(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLACK_LIVES_MATTER);
        $stream = $this->createStream($resource);
        self::assertTrue($stream->isReadable());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     */
    public function testIsSeekableReturnsWhetherOrNotTheStreamIsSeekable(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLACK_LIVES_MATTER);
        $stream = $this->createStream($resource);
        self::assertTrue($stream->isSeekable());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::isWritable
     */
    public function testIsWritable(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLACK_LIVES_MATTER);
        $stream = $this->createStream($resource);
        self::assertTrue($stream->isWritable());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::isReadable
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::read
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
    ┴
     */
    public function testRead(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLACK_LIVES_MATTER);
        $stream = $this->createStream($resource);
        $stream->rewind();

        $data = $stream->read(6);
        self::assertSame('#Black', $data);

        $data = $stream->read(11);
        self::assertSame('LivesMatter', $data);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::__toString
     * @covers \Ghostwriter\Http\Message\Stream::getContents
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     */
    public function testReadsAllDataFromTheStreamIntoAStringFromTheBeginningToEnd(): void
    {
        $stream = $this->createStream(fopen(__FILE__, 'r+b'));

        self::assertStringEqualsFile(__FILE__, $stream->__toString());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     */
    public function testRewind(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLACK_LIVES_MATTER);
        $stream = $this->createStream($resource);
        $stream->rewind();

        self::assertSame(self::BLACK_LIVES_MATTER, fread($resource, mb_strlen(self::BLACK_LIVES_MATTER)));
    }

    /**
     * @group internet
     *
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     */
    public function testRewindNotSeekable(): void
    {
        $this->expectException(RuntimeException::class);

        $resource = fopen(self::IMG_URL, 'rb');
        $stream = $this->createStream($resource);
        $stream->rewind();
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     */
    public function testSeekSeekToAPositionInTheStream(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLACK_LIVES_MATTER);
        $stream = $this->createStream($resource);
        $stream->seek(3);

        self::assertSame('ack', fread($resource, 3));
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::detach
     * @covers \Ghostwriter\Http\Message\Stream::fromString
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     */
    public function testSeekThrowsStreamIsInAnUnusableStateExceptionOnFailure(): void
    {
        $stream = Stream::fromString(self::BLACK_LIVES_MATTER);
        $stream->detach();
        $this->expectException(StreamIsInAnUnusableStateException::class);
        $stream->seek(3);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     */
    public function testSeekThrowsStreamIsNotSeekableExceptionOnFailure(): void
    {
        $resource = fopen(self::IMG_URL, 'rb');
        $stream = $this->createStream($resource);
        $this->expectException(StreamIsNotSeekableException::class);
        $stream->seek(PHP_INT_MAX, SEEK_END);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::fromString
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     */
    public function testSeekThrowsUnableToSeekInStreamExceptionOnFailure(): void
    {
        $stream = Stream::fromString(self::BLACK_LIVES_MATTER);
        $this->expectException(UnableToSeekInStreamException::class);
        $stream->seek(PHP_INT_MAX, SEEK_END);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::tell
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     * @covers \Ghostwriter\Http\Message\Stream::write
     */
    public function testTell(): void
    {
        $stream = new Stream('php://memory', 'r+b');
        $stream->write(self::BLACK_LIVES_MATTER);

        self::assertSame(17, $stream->tell());
        $stream->seek(0);
        self::assertSame(0, $stream->tell());
        $stream->seek(3);
        self::assertSame(3, $stream->tell());
        $stream->seek(6);
        self::assertSame(6, $stream->tell());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::tell
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     */
    public function testTellReturnsTheCurrentPositionOfTheFileReadWritePointer(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLACK_LIVES_MATTER);
        $stream = $this->createStream($resource);
        self::assertSame(17, $stream->tell());
        $stream->seek(0);
        self::assertSame(0, $stream->tell());
        $stream->seek(3);
        self::assertSame(3, $stream->tell());
        $stream->seek(6);
        self::assertSame(6, $stream->tell());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::detach
     * @covers \Ghostwriter\Http\Message\Stream::fromString
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     * @covers \Ghostwriter\Http\Message\Stream::tell
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     */
    public function testTellThrowsRuntimeExceptionOnError(): void
    {
        $stream = Stream::fromString(self::BLACK_LIVES_MATTER);
        $stream->detach();
        $this->expectException(StreamIsInAnUnusableStateException::class);
        $stream->tell();
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::detach
     * @covers \Ghostwriter\Http\Message\Stream::fromString
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     * @covers \Ghostwriter\Http\Message\Stream::tell
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::__toString
     * @covers \Ghostwriter\Http\Message\Stream::getContents
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::write
     */
    public function testToStringMethodMustAttemptToSeekToTheBeginningOfTheStreamBeforeReadingDataAndReadTheStreamUntilTheEndIsReached(): void
    {
        $stream = new Stream('php://temp', 'r+b');
        $stream->write(self::BML);
        self::assertSame(self::BML, $stream->__toString());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::__toString
     * @covers \Ghostwriter\Http\Message\Stream::getContents
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     */
    public function testToStringReadOnlyStreams(): void
    {
        $resource = fopen(__FILE__, 'rb');
        $stream = $this->createStream($resource);

        // Make sure this does not throw exception
        $content = (string) $stream;
        self::assertNotEmpty($content, 'You MUST be able to convert a read only stream to string');
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::__toString
     * @covers \Ghostwriter\Http\Message\Stream::getContents
     * @covers \Ghostwriter\Http\Message\Stream::isReadable
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     */
    public function testToStringRewindStreamBeforeToString(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLACK_LIVES_MATTER);
        fseek($resource, 3);
        $stream = $this->createStream($resource);

        $content = (string) $stream;
        self::assertSame(self::BLACK_LIVES_MATTER, $content);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::__toString
     * @covers \Ghostwriter\Http\Message\Stream::getContents
     * @covers \Ghostwriter\Http\Message\Stream::isReadable
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::validateResource
     * @covers \Ghostwriter\Http\Message\Stream::isWritable
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::streamIsUsable
     * @covers \Ghostwriter\Http\Message\Stream::write
     */
    public function testWrite(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, '#Black');
        $stream = $this->createStream($resource);
        $bytes = $stream->write('LivesMatter');

        self::assertSame(11, $bytes);
        self::assertSame(self::BLACK_LIVES_MATTER, (string) $stream);
    }
}
