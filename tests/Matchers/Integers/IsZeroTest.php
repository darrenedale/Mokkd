<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Integers;

use Mokkd\Matchers\Integers\IsZero;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(IsZero::class)]
class IsZeroTest extends TestCase
{
    use CreatesNullSerialiser;

    /** Ensure a zero int matches successfully. */
    public function testMatches1(): void
    {
        self::assertTrue((new IsZero())->matches(0));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield from DataFactory::negativeIntegers();
        yield from DataFactory::positiveIntegers();
    }

    /** Ensure a reasonable subset of non-zero ints don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(int $value): void
    {
        self::assertFalse((new IsZero())->matches($value));
    }

    /** Ensure a zero float does not match. */
    public function testMatches3(): void
    {
        self::assertFalse((new IsZero())->matches(0.0));
    }

    public static function dataForTestMatches4(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::singleWordStrings();
        yield from DataFactory::singleCharacterStrings();
        yield from DataFactory::zeroIntegerString();
        yield from DataFactory::arrays();
        yield from DataFactory::floats();
        yield from DataFactory::booleans();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
    }

    /** Ensure a reasonable subset of non-ints don't match. */
    #[DataProvider("dataForTestMatches4")]
    public function testMatches4(mixed $value): void
    {
        self::assertFalse((new IsZero())->matches($value));
    }

    /** Ensure the matcher describes itself as expected. */
    public static function testDescribe1(): void
    {
        self::assertSame("(int) == 0", (new IsZero())->describe(self::nullSerialiser()));
    }
}
