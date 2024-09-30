<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Floats;

use Mokkd\Matchers\Floats\IsEqualTo;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(IsEqualTo::class)]
class IsEqualToTest extends TestCase
{
    use CreatesNullSerialiser;

    private const SmallDifference = 0.00000001;

    private const LargeDifference = 4270.42435525;

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::combine(DataFactory::floats(), DataFactory::floats());
    }

    /** Ensure floats equal to the constraint match successfully. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(float $constraint, float $testValue): void
    {
        self::assertTrue((new IsEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches2(): iterable
    {
        foreach (DataFactory::floats() as $label => $args) {
            $value = $args[0];
            yield "{$label}-greater-than-large-difference" => [$value, $value + self::LargeDifference];
            yield "{$label}-less-than-large-difference" => [$value, $value - self::LargeDifference];
        }
    }

    /** Ensure floats that differ from the constraint value by a large quantity don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(float $constraint, float $testValue): void
    {
        self::assertFalse((new IsEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches3(): iterable
    {
        foreach (DataFactory::floats() as $label => $args) {
            $value = $args[0];
            yield "{$label}-greater-than-small-difference" => [$value, $value + self::SmallDifference];
            yield "{$label}-less-than-small-difference" => [$value, $value - self::SmallDifference];
        }
    }

    /** Ensure floats that differ from the constraint value by a small quantity don't match. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(float $constraint, float $testValue): void
    {
        self::assertFalse((new IsEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches6(): iterable
    {
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::nullValue());
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::singleWordStrings());
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::singleCharacterStrings());
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::floatStrings());
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::arrays());
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::positiveIntegers(20));
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::negativeIntegers(-20));
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::booleans());
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::objects());
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::resources());
    }

    /** Ensure a reasonable subset of non-floats don't match. */
    #[DataProvider("dataForTestMatches6")]
    public function testMatches6(float $constraint, mixed $value): void
    {
        self::assertFalse((new IsEqualTo($constraint))->matches($value));
    }

    public static function dataForTestDescribe1(): iterable
    {
        yield "zero" => [0.0, "(float) == 0.0"];
        yield "negative-integral-float" => [-2.0, "(float) == -2.0"];
        yield "positive-integral-float" => [5.0, "(float) == 5.0"];
        yield "negative-float" => [-7.5812000, "(float) == -7.5812"];
        yield "positive-float" => [3.1415926, "(float) == 3.1415926"];
    }

    /** Ensure the matcher describes itself as expected. */
    #[DataProvider("dataForTestDescribe1")]
    public function testDescribe1(float $constraint, string $expected): void
    {
        self::assertSame($expected, (new IsEqualTo($constraint))->describe(self::nullSerialiser()));
    }
}
