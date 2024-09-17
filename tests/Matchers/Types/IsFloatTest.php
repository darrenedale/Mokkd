<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Types;

use Mokkd\Matchers\Types\IsFloat;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsFloatTest extends TestCase
{
    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::floats();
    }

    /** Ensure all ints (or a sensible approximation of the set of floats) successfully match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(float $test): void
    {
        self::assertTrue((new IsFloat())->matches($test));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::strings();
        yield from DataFactory::booleans();
        yield from DataFactory::integers();
        yield from DataFactory::arrays();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
    }

    /** Ensure non-floats fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $test): void
    {
        self::assertFalse((new IsFloat())->matches($test));
    }

    /** Ensure the IsFloat matcher describes itself as expected. */
    public function testDescribe1(): void
    {
        self::assertSame("(float) {any}", (new IsFloat())->describe(self::nullSerialiser()));
    }
}
