<?php

declare(strict_types=1);

namespace Tests\Unit\Container;

use Ghostwriter\Container\Interface\BuilderInterface;
use Ghostwriter\Container\Interface\Service\ProviderInterface;
use Ghostwriter\Container\Service\Provider\AbstractProvider;
use Ghostwriter\Http\Container\HttpProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;

use function array_keys;
use function array_map;
use function array_merge;
use function array_values;
use function count;
use function is_a;

#[CoversClass(HttpProvider::class)]
final class HttpProviderTest extends AbstractTestCase
{
    public function testExtendsAbstractProvider(): void
    {
        self::assertTrue(is_a(HttpProvider::class, AbstractProvider::class, true));
    }

    public function testHttpProviderRegister(): void
    {
        $builder = $this->createMock(BuilderInterface::class);

        $parameters = array_merge(...array_map(
            static fn (string $alias, string $service): array => [[$alias, $service]],
            array_keys(HttpProvider::ALIAS),
            array_values(HttpProvider::ALIAS),
        ));

        $builder->expects(self::exactly(count($parameters)))
            ->method('alias')
            ->withParameterSetsInOrder(...$parameters);

        $builder->expects(self::never())->method('bind')->withAnyParameters();
        $builder->expects(self::never())->method('extend')->withAnyParameters();
        $builder->expects(self::never())->method('factory')->withAnyParameters();
        $builder->expects(self::never())->method('unset')->withAnyParameters();
        $builder->expects(self::never())->method('set')->withAnyParameters()->seal();

        (new HttpProvider())->register($builder);
    }

    public function testImplementsProviderInterface(): void
    {
        self::assertTrue(is_a(HttpProvider::class, ProviderInterface::class, true));
    }
}
