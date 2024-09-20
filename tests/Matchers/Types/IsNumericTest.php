<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Types;

use Mokkd\Matchers\Types\IsNumeric;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsNumericTest extends TestCase
{
    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::integers();
        yield from DataFactory::floats();
    }

    /** Ensure all ints and floats (or a reasonable approximation of the set of ints and floats) successfully match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(int|float $test): void
    {
        self::assertTrue((new IsNumeric())->matches($test));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::strings();
        yield from DataFactory::booleans();
        yield from DataFactory::arrays();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
    }

    /** Ensure non-bools fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $test): void
    {
        self::assertFalse((new IsNumeric())->matches($test));
    }

    /** Ensure the IsNumeric matcher describes itself as expected. */
    public function testDescribe1(): void
    {
        self::assertSame("(int|float) {any}", (new IsNumeric())->describe(self::nullSerialiser()));
    }
}
