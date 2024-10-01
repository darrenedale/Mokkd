<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Floats;

use Mokkd\Matchers\Floats\IsNotZero;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(IsNotZero::class)]
class IsNotZeroTest extends TestCase
{
    use CreatesNullSerialiser;

    /** Ensure a zero float does not match. */
    public function testMatches1(): void
    {
        self::assertFalse((new IsNotZero())->matches(0.0));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield from DataFactory::negativeFloats();
        yield from DataFactory::positiveFloats();
    }

    /** Ensure a reasonable subset of non-zero floats match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(float $value): void
    {
        self::assertTrue((new IsNotZero())->matches($value));
    }

    public static function dataForTestMatches3(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::singleWordStrings();
        yield from DataFactory::singleCharacterStrings();
        yield from DataFactory::floatStrings();
        yield from DataFactory::arrays();
        yield from DataFactory::integers();
        yield from DataFactory::booleans();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
    }

    /** Ensure a reasonable subset of non-floats don't match. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(mixed $value): void
    {
        self::assertFalse((new IsNotZero())->matches($value));
    }

    /** Ensure the matcher describes itself as expected. */
    public static function testDescribe1(): void
    {
        self::assertSame("(float) != 0.0", (new IsNotZero())->describe(self::nullSerialiser()));
    }
}
