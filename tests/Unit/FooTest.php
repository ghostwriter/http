<?php

declare(strict_types=1);

namespace Ghostwriter\Http\Tests\Unit;

use Ghostwriter\Http\Foo;

/**
 * @coversDefaultClass \Ghostwriter\Http\Foo
 *
 * @internal
 *
 * @small
 */
final class FooTest extends AbstractTestCase
{
    /** @covers ::test */
    public function test(): void
    {
        self::assertTrue((new Foo())->test());
    }
}
