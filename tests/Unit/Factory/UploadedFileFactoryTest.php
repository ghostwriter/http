<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Tests\Unit\Factory;

use Ghostwriter\Http\Contract\Factory\UploadedFileFactoryInterface;
use Ghostwriter\Http\Contract\Message\StreamInterface;
use Ghostwriter\Http\Factory\StreamFactory;
use Ghostwriter\Http\Factory\UploadedFileFactory;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Ghostwriter\Http\Factory\UploadedFileFactory
 *
 * @internal
 *
 * @small
 */
final class UploadedFileFactoryTest extends TestCase
{
    /**
     * @covers \Ghostwriter\Http\Message\UploadedFile::__construct
     * @covers \Ghostwriter\Http\Factory\UploadedFileFactory::createUploadedFile
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(UploadedFileFactoryInterface::class, $this->createUploadedFileFactory());
    }

    /**
     * @covers \Ghostwriter\Http\Factory\UploadedFileFactory
     */
    public function testCreateUploadedFile(): void
    {
        $uploadedFileFactory = $this->createUploadedFileFactory();
        self::assertSame($uploadedFileFactory, $uploadedFileFactory);
    }

    private function createStream(string $content = ''): StreamInterface
    {
        $file = tempnam(sys_get_temp_dir(), 'UploadedFileFactoryTest');
        $resource = fopen($file, 'r+b');
        fwrite($resource, $content);
        rewind($resource);
        return (new StreamFactory())->createStreamFromResource($resource);
    }

    private function createUploadedFileFactory(): UploadedFileFactory
    {
        return new UploadedFileFactory();
    }
}
