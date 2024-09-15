<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Types;

use Mokkd\Matchers\Types\IsNull;
use MokkdTests\CreatesMockSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsNullTest extends TestCase
{
    use CreatesMockSerialiser;

    private const MockSerialisation = "A null value";

    /** Ensure null successfully matches. */
    public function testMatches1(): void
    {
        self::assertTrue((new IsNull())->matches(null));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield from DataFactory::arrays();
        yield from DataFactory::strings();
        yield from DataFactory::integers();
        yield from DataFactory::floats();
        yield from DataFactory::booleans();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
    }

    /** Ensure non-nulls fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $test): void
    {
        self::assertFalse((new IsNull())->matches($test));
    }

    /** Ensure the IsNull matcher uses the provided serialiser to serialise the expected (null) value. */
    public function testDescribe1(): void
    {
        self::assertSame(
            self::MockSerialisation,
            (new IsNull())->describe(self::mockSerialiser(null, self::MockSerialisation)),
        );
    }
}
