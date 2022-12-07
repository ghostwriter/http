<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Tests\Unit\Message;

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
    private const BLM = '#BlackLivesMatter';

    /**
     * @var string
     */
    private const IMG_URL = 'https://github.com/ghostwriter.png';

    /**
     * @param null|resource|string $data
     */
    public function createStream(mixed $data): Stream
    {
        return new Stream($data);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::close
     * @covers \Ghostwriter\Http\Message\Stream::detach
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
     */
    public function testClose(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLM);
        $stream = $this->createStream($resource);

        self::assertIsResource($resource);
        $stream->close();
        self::assertIsClosedResource($resource);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::detach
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
     */
    public function testDetach(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, 'abc');
        $stream = $this->createStream($resource);

        self::assertSame($resource, $stream->detach());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::eof
     * @covers \Ghostwriter\Http\Message\Stream::isReadable
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
     * @covers \Ghostwriter\Http\Message\Stream::read
     * @covers \Ghostwriter\Http\Message\Stream::seek
     */
    public function testEof(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLM);
        $stream = $this->createStream($resource);

        $stream->seek(0);
        self::assertFalse($stream->eof());
        $stream->read(20);
        $stream->read(10);
        self::assertTrue($stream->eof());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::getContents
     * @covers \Ghostwriter\Http\Message\Stream::isReadable
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
     */
    public function testGetContents(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLM);

        $stream = $this->createStream($resource);
        $stream->rewind();

        $stream->seek(6);
        self::assertSame('LivesMatter', $stream->getContents());
        self::assertSame('', $stream->getContents());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::getSize
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
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
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::isReadable
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
     */
    public function testIsNotReadable(): void
    {
        $resource = fopen(self::IMG_URL, 'rb');
        $stream = $this->createStream($resource);
        self::assertTrue($stream->isReadable());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
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
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
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
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::isReadable
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
     */
    public function testIsReadable(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLM);
        $stream = $this->createStream($resource);
        self::assertTrue($stream->isReadable());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
     */
    public function testIsSeekable(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLM);
        $stream = $this->createStream($resource);
        self::assertTrue($stream->isSeekable());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
     * @covers \Ghostwriter\Http\Message\Stream::isWritable
     */
    public function testIsWritable(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLM);
        $stream = $this->createStream($resource);
        self::assertTrue($stream->isWritable());
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::isReadable
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
     * @covers \Ghostwriter\Http\Message\Stream::read
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
    ┴
     */
    public function testRead(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLM);
        $stream = $this->createStream($resource);
        $stream->rewind();

        $data = $stream->read(6);
        self::assertSame('#Black', $data);

        $data = $stream->read(11);
        self::assertSame('LivesMatter', $data);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
     */
    public function testRewind(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLM);
        $stream = $this->createStream($resource);
        $stream->rewind();

        self::assertSame(self::BLM, fread($resource, mb_strlen(self::BLM)));
    }

    /**
     * @group internet
     *
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
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
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
     * @covers \Ghostwriter\Http\Message\Stream::seek
     */
    public function testSeek(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLM);
        $stream = $this->createStream($resource);
        $stream->seek(3);

        self::assertSame('ack', fread($resource, 3));
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::tell
     */
    public function testTell(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLM);
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
     * @covers \Ghostwriter\Http\Message\Stream::__toString
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::getContents
     * @covers \Ghostwriter\Http\Message\Stream::isReadable
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
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
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::getContents
     * @covers \Ghostwriter\Http\Message\Stream::isReadable
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
     */
    public function testToStringRewindStreamBeforeToString(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, self::BLM);
        fseek($resource, 3);
        $stream = $this->createStream($resource);

        $content = (string) $stream;
        self::assertSame(self::BLM, $content);
    }

    /**
     * @covers \Ghostwriter\Http\Message\Stream::__construct
     * @covers \Ghostwriter\Http\Message\Stream::__toString
     * @covers \Ghostwriter\Http\Message\Stream::attach
     * @covers \Ghostwriter\Http\Message\Stream::getContents
     * @covers \Ghostwriter\Http\Message\Stream::isReadable
     * @covers \Ghostwriter\Http\Message\Stream::isSeekable
     * @covers \Ghostwriter\Http\Message\Stream::isValidStreamResourceType
     * @covers \Ghostwriter\Http\Message\Stream::isWritable
     * @covers \Ghostwriter\Http\Message\Stream::rewind
     * @covers \Ghostwriter\Http\Message\Stream::seek
     * @covers \Ghostwriter\Http\Message\Stream::write
     */
    public function testWrite(): void
    {
        $resource = fopen('php://memory', 'rwb');
        fwrite($resource, '#Black');
        $stream = $this->createStream($resource);
        $bytes = $stream->write('LivesMatter');

        self::assertSame(11, $bytes);
        self::assertSame(self::BLM, (string) $stream);
    }
}
