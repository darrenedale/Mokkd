<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Numerics;

use Mokkd\Matchers\Numerics\IsNotEqualTo;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function ceil;
use function floor;

#[CoversClass(IsNotEqualTo::class)]
class IsNotEqualToTest extends TestCase
{
    use CreatesNullSerialiser;

    private const SmallIntDifference = 1;

    private const LargeIntDifference = 5000;

    private const SmallFloatDifference = 0.00000001;

    private const LargeFloatDifference = 4270.42435525;


    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::combine(DataFactory::integers(-20, 20), DataFactory::integers(-20, 20));
    }

    /** Ensure ints equal to an int constraint don't match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(int $constraint, int $testValue): void
    {
        self::assertFalse((new IsNotEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield from DataFactory::combine(
            DataFactory::transform(DataFactory::integers(-20, 20), static fn (int $value): float => (float) $value),
            DataFactory::integers(-20, 20),
        );
    }

    /** Ensure ints equal to a float constraint don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(float $constraint, int $testValue): void
    {
        self::assertFalse((new IsNotEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches3(): iterable
    {
        yield from DataFactory::combine(
            DataFactory::integers(-20, 20),
            DataFactory::transform(DataFactory::integers(-20, 20), static fn (int $value): float => (float) $value),
        );
    }

    /** Ensure floats equal to an int constraint don't match. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(int $constraint, float $testValue): void
    {
        self::assertFalse((new IsNotEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches4(): iterable
    {
        yield from DataFactory::combine(DataFactory::floats(), DataFactory::floats());
    }

    /** Ensure floats equal to a float constraint don't match. */
    #[DataProvider("dataForTestMatches4")]
    public function testMatches4(float $constraint, float $testValue): void
    {
        self::assertFalse((new IsNotEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches5(): iterable
    {
        foreach (DataFactory::concatenate(DataFactory::negativeIntegers(-20), DataFactory::positiveIntegers(20), DataFactory::integerZero()) as $label => $args) {
            $value = $args[0];
            yield "{$label}-greater-than-large-difference" => [$value, $value + self::LargeIntDifference];
            yield "{$label}-less-than-large-difference" => [$value, $value - self::LargeIntDifference];
        }
    }

    /** Ensure ints that differ from an int constraint value by a large quantity match successfully. */
    #[DataProvider("dataForTestMatches5")]
    public function testMatches5(int $constraint, int $testValue): void
    {
        self::assertTrue((new IsNotEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches6(): iterable
    {
        foreach (DataFactory::concatenate(DataFactory::negativeIntegers(-20), DataFactory::positiveIntegers(20), DataFactory::integerZero()) as $label => $args) {
            $value = $args[0];
            yield "{$label}-greater-than-large-difference" => [$value, ((float) $value) + self::LargeFloatDifference];
            yield "{$label}-less-than-large-difference" => [$value, ((float) $value) - self::LargeFloatDifference];
        }
    }

    /** Ensure floats that differ from an int constraint value by a large quantity match successfully. */
    #[DataProvider("dataForTestMatches6")]
    public function testMatches6(int $constraint, float $testValue): void
    {
        self::assertTrue((new IsNotEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches7(): iterable
    {
        foreach (DataFactory::floats() as $label => $args) {
            $value = $args[0];
            yield "{$label}-greater-than-large-difference" => [$value, (int) ceil($value + self::LargeIntDifference)];
            yield "{$label}-less-than-large-difference" => [$value, (int) floor($value - self::LargeIntDifference)];
        }
    }

    /** Ensure ints that differ from a float constraint value by a large quantity match successfully. */
    #[DataProvider("dataForTestMatches7")]
    public function testMatches7(float $constraint, int $testValue): void
    {
        self::assertTrue((new IsNotEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches8(): iterable
    {
        foreach (DataFactory::floats() as $label => $args) {
            $value = $args[0];
            yield "{$label}-greater-than-large-difference" => [$value, $value + self::LargeFloatDifference];
            yield "{$label}-less-than-large-difference" => [$value, $value - self::LargeFloatDifference];
        }
    }

    /** Ensure floats that differ from a float constraint value by a large quantity match successfully. */
    #[DataProvider("dataForTestMatches8")]
    public function testMatches8(float $constraint, float $testValue): void
    {
        self::assertTrue((new IsNotEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches9(): iterable
    {
        foreach (DataFactory::concatenate(DataFactory::negativeIntegers(-20), DataFactory::positiveIntegers(20), DataFactory::integerZero()) as $label => $args) {
            $value = $args[0];
            yield "{$label}-greater-than-small-difference" => [$value, $value + self::SmallIntDifference];
            yield "{$label}-less-than-small-difference" => [$value, $value - self::SmallIntDifference];
        }
    }

    /** Ensure ints that differ from an int constraint value by a small quantity match successfully. */
    #[DataProvider("dataForTestMatches9")]
    public function testMatches9(int $constraint, int $testValue): void
    {
        self::assertTrue((new IsNotEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches10(): iterable
    {
        foreach (DataFactory::concatenate(DataFactory::negativeIntegers(-20), DataFactory::positiveIntegers(20), DataFactory::integerZero()) as $label => $args) {
            $value = $args[0];
            yield "{$label}-greater-than-small-difference" => [$value, ((float) $value) + self::SmallFloatDifference];
            yield "{$label}-less-than-small-difference" => [$value, ((float) $value) - self::SmallFloatDifference];
        }
    }

    /** Ensure floats that differ from an int constraint value by a small quantity match successfully. */
    #[DataProvider("dataForTestMatches10")]
    public function testMatches10(int $constraint, float $testValue): void
    {
        self::assertTrue((new IsNotEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches11(): iterable
    {
        foreach (DataFactory::floats() as $label => $args) {
            $value = $args[0];
            yield "{$label}-greater-than-small-difference" => [$value, (int) ceil($value + self::SmallIntDifference)];
            yield "{$label}-less-than-small-difference" => [$value, (int) floor($value - self::SmallIntDifference)];
        }
    }

    /** Ensure ints that differ from a float constraint value by a small quantity match successfully. */
    #[DataProvider("dataForTestMatches11")]
    public function testMatches11(float $constraint, int $testValue): void
    {
        self::assertTrue((new IsNotEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches12(): iterable
    {
        foreach (DataFactory::floats() as $label => $args) {
            $value = $args[0];
            yield "{$label}-greater-than-small-difference" => [$value, $value + self::SmallFloatDifference];
            yield "{$label}-less-than-small-difference" => [$value, $value - self::SmallFloatDifference];
        }
    }

    /** Ensure floats that differ from a float constraint value by a small quantity match successfully. */
    #[DataProvider("dataForTestMatches12")]
    public function testMatches12(float $constraint, float $testValue): void
    {
        self::assertTrue((new IsNotEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches13(): iterable
    {
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::nullValue());
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::singleWordStrings());
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::singleCharacterStrings());
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::integerStrings(-20, 20));
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::floatStrings());
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::arrays());
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::booleans());
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::objects());
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::resources());
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::nullValue());
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::singleWordStrings());
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::singleCharacterStrings());
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::integerStrings(-20, 20));
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::floatStrings());
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::arrays());
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::booleans());
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::objects());
        yield from DataFactory::matrix(DataFactory::floats(), DataFactory::resources());
    }

    /** Ensure a reasonable subset of non-numerics don't match. */
    #[DataProvider("dataForTestMatches13")]
    public function testMatches13(int|float $constraint, mixed $value): void
    {
        self::assertFalse((new IsNotEqualTo($constraint))->matches($value));
    }

    public static function dataForTestDescribe1(): iterable
    {
        yield "zero" => [0, "(int|float) != 0"];
        yield "negative-float" => [-2.50183, "(int|float) != -2.50183"];
        yield "positive-float" => [5.0, "(int|float) != 5.0"];
        yield "negative-int" => [-7, "(int|float) != -7"];
        yield "positive-int" => [42, "(int|float) != 42"];
    }

    /** Ensure the matcher describes itself as expected. */
    #[DataProvider("dataForTestDescribe1")]
    public function testDescribe1(int|float $constraint, string $expected): void
    {
        self::assertSame($expected, (new IsNotEqualTo($constraint))->describe(self::nullSerialiser()));
    }
}
