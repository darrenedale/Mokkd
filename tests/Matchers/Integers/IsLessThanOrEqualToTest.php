<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Integers;

use Mokkd\Matchers\Integers\IsLessThanOrEqualTo;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\Matchers\RelabelMode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(IsLessThanOrEqualTo::class)]
class IsLessThanOrEqualToTest extends TestCase
{
    private const SmallDifference = 1;

    private const LargeDifference = 5000;

    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        foreach ([...DataFactory::negativeIntegers(), ...DataFactory::positiveIntegers(), ...DataFactory::integerZero(), ...DataFactory::maxInteger()] as $label => $args) {
            $value = $args[0];
            yield "{$label}-less-than-small-difference" => [$value, $value - self::SmallDifference];
        }
    }

    /** Ensure ints less than the constraint value by a small quantity match successfully. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(int $constraint, int $testValue): void
    {
        self::assertTrue((new IsLessThanOrEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches2(): iterable
    {
        foreach ([...DataFactory::negativeIntegers(-20), ...DataFactory::positiveIntegers(20), ...DataFactory::integerZero(), ...DataFactory::maxInteger()] as $label => $args) {
            $value = $args[0];
            yield "{$label}-less-than-large-difference" => [$value, $value - self::LargeDifference];
        }
    }

    /** Ensure ints less than the constraint value by a large quantity match successfully. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(int $constraint, int $testValue): void
    {
        self::assertTrue((new IsLessThanOrEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches3(): iterable
    {
        foreach ([...DataFactory::negativeIntegers(-20), ...DataFactory::positiveIntegers(20), ...DataFactory::integerZero(), ...DataFactory::minInteger()] as $label => $args) {
            $value = $args[0];
            yield "{$label}-greater-than-small-difference" => [$value, $value + self::SmallDifference];
        }
    }

    /** Ensure ints greater than the constraint value by a small quantity don't match. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(int $constraint, int $testValue): void
    {
        self::assertFalse((new IsLessThanOrEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches4(): iterable
    {
        foreach ([...DataFactory::negativeIntegers(-20), ...DataFactory::positiveIntegers(20), ...DataFactory::integerZero(), ...DataFactory::minInteger()] as $label => $args) {
            $value = $args[0];
            yield "{$label}-less-than-large-difference" => [$value, $value + self::LargeDifference];
        }
    }

    /** Ensure ints greater than the constraint value by a large quantity don't match. */
    #[DataProvider("dataForTestMatches4")]
    public function testMatches4(int $constraint, int $testValue): void
    {
        self::assertFalse((new IsLessThanOrEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches5(): iterable
    {
        yield from DataFactory::relabel(DataFactory::integers(-20, 20), "-equal", RelabelMode::Suffix);
    }

    /** Ensure ints equal to the constraint value match successfully. */
    #[DataProvider("dataForTestMatches5")]
    public function testMatches5(int $constraint): void
    {
        self::assertTrue((new IsLessThanOrEqualTo($constraint))->matches($constraint));
    }

    public static function dataForTestMatches6(): iterable
    {
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::nullValue());
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::singleWordStrings());
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::singleCharacterStrings());
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::integerStrings(-20, 20));
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::arrays());
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::floats());
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::booleans());
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::objects());
        yield from DataFactory::matrix(DataFactory::integers(-20, 20), DataFactory::resources());
    }

    /** Ensure a reasonable subset of non-ints don't match. */
    #[DataProvider("dataForTestMatches6")]
    public function testMatches6(int $constraint, mixed $value): void
    {
        self::assertFalse((new IsLessThanOrEqualTo($constraint))->matches($value));
    }

    public static function dataForTestDescribe1(): iterable
    {
        yield "zero" => [0, "(int) <= 0"];
        yield "negative-int" => [-2, "(int) <= -2"];
        yield "positive-int" => [42, "(int) <= 42"];
    }

    /** Ensure the matcher describes itself as expected. */
    #[DataProvider("dataForTestDescribe1")]
    public function testDescribe1(int $constraint, string $expected): void
    {
        self::assertSame($expected, (new IsLessThanOrEqualTo($constraint))->describe(self::nullSerialiser()));
    }
}
