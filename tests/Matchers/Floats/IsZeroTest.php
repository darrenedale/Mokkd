<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Floats;

use Mokkd\Matchers\Floats\IsZero;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(IsZero::class)]
class IsZeroTest extends TestCase
{
    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::floatZero();
    }

    /** Ensure a zero float matches successfully. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(float $value): void
    {
        self::assertTrue((new IsZero())->matches($value));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield from DataFactory::negativeFloats();
        yield from DataFactory::positiveFloats();
    }

    /** Ensure a reasonable subset of non-zero floats don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(float $value): void
    {
        self::assertFalse((new IsZero())->matches($value));
    }

    /** Ensure a zero int does not match. */
    public function testMatches3(): void
    {
        self::assertFalse((new IsZero())->matches(0));
    }

    public static function dataForTestMatches4(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::strings();
        yield from DataFactory::arrays();
        yield from DataFactory::positiveIntegers();
        yield from DataFactory::negativeIntegers();
        yield from DataFactory::booleans();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
    }

    /** Ensure a reasonable subset of non-floats don't match. */
    #[DataProvider("dataForTestMatches4")]
    public function testMatches4(mixed $value): void
    {
        self::assertFalse((new IsZero())->matches($value));
    }

    /** Ensure the matcher describes itself as expected. */
    public static function testDescribe1(): void
    {
        self::assertSame("(float) == 0.0", (new IsZero())->describe(self::nullSerialiser()));
    }
}
