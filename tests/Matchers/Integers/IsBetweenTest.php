<?php

/*
 * Copyright 2025 Darren Edale
 *
 * This file is part of the Mokkd package.
 *
 * Mokkd is free software: you can redistribute it and/or modify
 * it under the terms of the Apache License v2.0.
 *
 * Mokkd is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * Apache License for more details.
 *
 * You should have received a copy of the Apache License v2.0
 * along with Mokkd. If not, see <http://www.apache.org/licenses/>.
 */

declare(strict_types=1);

namespace MokkdTests\Matchers\Integers;

use Mokkd\Matchers\Integers\IsBetween;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(IsBetween::class)]
class IsBetweenTest extends TestCase
{
    private const SmallDifference = 1;

    private const SmallRange = self::SmallDifference * 2;

    private const LargeDifference = 5000;

    private const LargeRange = self::LargeDifference * 2;

    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        foreach ([...DataFactory::negativeIntegers(-20), ...DataFactory::positiveIntegers(20), ...DataFactory::integerZero(), ...DataFactory::minInteger()] as $label => $args) {
            $value = $args[0];
            yield "{$label}-between-small-range" => [$value, $value + self::SmallRange, $value + self::SmallDifference];
        }
    }

    /** Ensure ints between the bounds with small ranges match successfully. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(int $lowerBound, int $upperBound, int $testValue): void
    {
        self::assertTrue((new IsBetween($lowerBound, $upperBound))->matches($testValue));
    }

    public static function dataForTestMatches2(): iterable
    {
        foreach ([...DataFactory::negativeIntegers(-20), ...DataFactory::positiveIntegers(20), ...DataFactory::integerZero(), ...DataFactory::minInteger()] as $label => $args) {
            $value = $args[0];
            yield "{$label}-between-large-range" => [$value, $value + self::LargeRange,  $value + self::LargeDifference];
        }
    }

    /** Ensure ints between the bounds with large ranges match successfully. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(int $lowerBound, int $upperBound, int $testValue): void
    {
        self::assertTrue((new IsBetween($lowerBound, $upperBound))->matches($testValue));
    }

    public static function dataForTestMatches3(): iterable
    {
        foreach ([...DataFactory::negativeIntegers(-20), ...DataFactory::positiveIntegers(20), ...DataFactory::integerZero(), ...DataFactory::minInteger()] as $label => $args) {
            $value = $args[0];
            yield "{$label}-between-large-range-small-difference-lower" => [$value, $value + self::LargeRange,  $value + self::SmallDifference];
            yield "{$label}-between-large-range-small-difference-upper" => [$value, $value + self::LargeRange,  $value + self::LargeRange - self::SmallDifference];
        }
    }

    /** Ensure ints between the bounds with large ranges and values close to the bound match successfully. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(int $lowerBound, int $upperBound, int $testValue): void
    {
        self::assertTrue((new IsBetween($lowerBound, $upperBound))->matches($testValue));
    }

    public static function dataForTestMatches4(): iterable
    {
        foreach ([...DataFactory::negativeIntegers(-20), ...DataFactory::positiveIntegers(20), ...DataFactory::integerZero(), ...DataFactory::minInteger()] as $label => $args) {
            $value = $args[0];
            yield "{$label}-between-large-range-equal-lower" => [$value, $value + self::LargeRange,  $value];
            yield "{$label}-between-large-range-equal-upper" => [$value, $value + self::LargeRange,  $value + self::LargeRange];
        }
    }

    /** Ensure ints on the bounds match successfully. */
    #[DataProvider("dataForTestMatches4")]
    public function testMatches4(int $lowerBound, int $upperBound, int $testValue): void
    {
        self::assertTrue((new IsBetween($lowerBound, $upperBound))->matches($testValue));
    }
    
    public static function dataForTestMatches5(): iterable
    {
        foreach ([...DataFactory::negativeIntegers(-20), ...DataFactory::positiveIntegers(20), ...DataFactory::integerZero()] as $label => $args) {
            $value = $args[0];
            yield "{$label}-less-than-lower-bound-small-difference" => [$value, $value + self::SmallRange, $value - self::SmallDifference];
            yield "{$label}-greater-than-upper-bound-small-difference" => [$value, $value + self::SmallRange, $value + self::SmallRange + self::SmallDifference];
        }
    }

    /** Ensure ints less than the lower bound or greater than the upper bound by a small quantity don't match. */
    #[DataProvider("dataForTestMatches5")]
    public function testMatches5(int $lowerBound, int $upperBound, int $testValue): void
    {
        self::assertFalse((new IsBetween($lowerBound, $upperBound))->matches($testValue));
    }
    
    public static function dataForTestMatches6(): iterable
    {
        foreach ([...DataFactory::negativeIntegers(-20), ...DataFactory::positiveIntegers(20), ...DataFactory::integerZero()] as $label => $args) {
            $value = $args[0];
            yield "{$label}-less-than-lower-bound-large-difference" => [$value, $value + self::LargeRange, $value - self::LargeDifference];
            yield "{$label}-greater-than-upper-bound-large-difference" => [$value, $value + self::LargeRange, $value + self::LargeRange + self::LargeDifference];
        }
    }

    /** Ensure ints less than the lower bound or greater than the upper bound by a large quantity don't match. */
    #[DataProvider("dataForTestMatches6")]
    public function testMatches6(int $lowerBound, int $upperBound, int $testValue): void
    {
        self::assertFalse((new IsBetween($lowerBound, $upperBound))->matches($testValue));
    }

    public static function dataForTestMatches7(): iterable
    {
        // zero within range, small and large range
        yield "float-zero-within-small" => [-self::SmallDifference, self::SmallDifference, 0.0];
        yield "float-zero-within-large" => [-self::LargeDifference, self::LargeDifference, 0.0];

        // range entirely in positive ints, small and large range, float within range
        yield "float-positive-within-positive-small" => [self::SmallDifference, self::SmallDifference + self::SmallRange, (float) self::SmallDifference + self::SmallDifference];
        yield "float-positive-within-positive-large" => [self::LargeDifference, self::LargeDifference + self::LargeRange, self::LargeDifference + self::LargeDifference];

        // range entirely in negative ints, small and large range, float within range
        yield "float-negative-within-negative-small" => [-self::SmallDifference - self::SmallRange, -self::SmallDifference, (float) -self::SmallDifference - self::SmallDifference];
        yield "float-negative-within-negative-large" => [-self::LargeDifference - self::LargeRange, -self::LargeDifference, (float) -self::LargeDifference - self::LargeDifference];

        // range spans negative and positive ints, small and large range, positive float within range
        yield "float-positive-within-negative-positive-small" => [-self::SmallDifference, self::SmallRange, (float) self::SmallDifference];
        yield "float-positive-within-negative-positive-large" => [-self::LargeDifference, self::LargeDifference, ((float) self::LargeDifference) / 2.0];

        // range spans negative and positive ints, small and large range, negative float within range
        yield "float-negative-within-negative-positive-small" => [-self::SmallRange, self::SmallDifference, (float) -self::SmallDifference];
        yield "float-negative-within-negative-positive-large" => [-self::LargeDifference, self::LargeDifference, -((float) self::LargeDifference / 2.0)];

        // one end of range is 0, small and large range, float matching lower and upper bound
        yield "float-zero-equal-lower-small" => [0, self::SmallDifference, 0.0];
        yield "float-zero-equal-lower-large" => [0, self::LargeDifference, 0.0];
        yield "float-zero-equal-upper-small" => [-self::SmallDifference, 0, 0.0];
        yield "float-zero-equal-upper-large" => [-self::LargeDifference, 0, 0.0];

        // range entirely in positive ints, small and large range, float matching lower and upper bound
        yield "float-positive-equal-lower-positive-small" => [self::SmallDifference, self::SmallDifference + self::SmallRange, (float) self::SmallDifference];
        yield "float-positive-equal-lower-positive-large" => [self::LargeDifference, self::LargeDifference + self::LargeRange, (float) self::LargeDifference];
        yield "float-positive-equal-upper-positive-small" => [self::SmallDifference, self::SmallDifference + self::SmallRange, (float) (self::SmallDifference+ self::SmallRange)];
        yield "float-positive-equal-upper-positive-large" => [self::LargeDifference, self::LargeDifference + self::LargeRange, (float) (self::LargeDifference + self::LargeRange)];

        // range entirely in negative ints, small and large range, float matching lower and upper bound
        yield "float-negative-equal-lower-negative-small" => [-self::SmallDifference - self::SmallRange, -self::SmallDifference, (float) (-self::SmallDifference - self::SmallRange)];
        yield "float-negative-equal-lower-negative-large" => [-self::LargeDifference - self::LargeRange, -self::LargeDifference, (float) (-self::LargeDifference - self::LargeRange)];
        yield "float-negative-equal-upper-negative-small" => [-self::SmallDifference - self::SmallRange, -self::SmallDifference, (float) -self::SmallDifference];
        yield "float-negative-equal-upper-negative-large" => [-self::LargeDifference - self::LargeRange, -self::LargeDifference, (float) -self::LargeDifference];

        // range spans negative and positive ints, small and large range, float matching lower and upper bound
        yield "float-positive-equal-lower-negative-positive-small" => [-self::SmallDifference, self::SmallDifference, (float) -self::SmallDifference];
        yield "float-positive-equal-lower-negative-positive-large" => [-self::LargeDifference, self::LargeDifference, (float) -self::LargeDifference];
        yield "float-positive-equal-upper-negative-positive-small" => [-self::SmallDifference, self::SmallDifference, (float) self::SmallDifference];
        yield "float-positive-equal-upper-negative-positive-large" => [-self::LargeDifference, self::LargeDifference, (float) self::LargeDifference];
    }

    /** Ensure floats between the int bounds don't match. */
    #[DataProvider("dataForTestMatches7")]
    public function testMatches7(int $lowerBound, int $upperBound, float $testValue): void
    {
        // first, prove that we're testing with a value consistent with the purpose of the test
        self::assertEquals((int) $testValue, $testValue, "{$testValue} != (int) {$testValue} (" . ((int) $testValue) . ")");
        $matcher = new IsBetween($lowerBound, $upperBound);
        self::assertTrue($matcher->matches((int) $testValue), "Integers\\IsBetween({$lowerBound}, {$upperBound}) does not match (int) {$testValue} (" . ((int) $testValue) . ")");

        self::assertFalse($matcher->matches($testValue));
    }

    public static function dataForTestMatches8(): iterable
    {
        foreach (["small" => self::SmallRange, "large" => self::LargeRange] as $rangeLabel => $rangeSize) {
            foreach ([
                         DataFactory::singleWordStrings(),
                         DataFactory::singleCharacterStrings(),
                         DataFactory::integerStrings(-20, 20),
                         DataFactory::arrays(),
                         DataFactory::floats(),
                         DataFactory::booleans(),
                         DataFactory::objects(),
                         DataFactory::resources(),
                     ] as $dataSource) {
                foreach (DataFactory::matrix([...DataFactory::negativeIntegers(-20), ...DataFactory::positiveIntegers(20), ...DataFactory::integerZero()], $dataSource) as $label => $args) {
                    $args = [$args[0], $args[0] + $rangeSize, $args[1]];
                    yield "{$label}-{$rangeLabel}-range" => $args;
                }
            }
        }
    }

    /** Ensure a reasonable subset of non-ints don't match. */
    #[DataProvider("dataForTestMatches8")]
    public function testMatches8(int $lowerBound, int $upperBound, mixed $value): void
    {
        self::assertFalse((new IsBetween($lowerBound, $upperBound))->matches($value));
    }

    public static function dataForTestDescribe1(): iterable
    {
        yield "zero-one" => [0, 1, "(int) >= 0 && <= 1"];
        yield "negative-int" => [-2, 0, "(int) >= -2 && <= 0"];
        yield "positive-int" => [42, 101, "(int) >= 42 && <= 101"];
    }

    /** Ensure the matcher describes itself as expected. */
    #[DataProvider("dataForTestDescribe1")]
    public function testDescribe1(int $lowerBound, int $upperBound, string $expected): void
    {
        self::assertSame($expected, (new IsBetween($lowerBound, $upperBound))->describe(self::nullSerialiser()));
    }
}
