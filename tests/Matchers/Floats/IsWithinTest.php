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

namespace MokkdTests\Matchers\Floats;

use Mokkd\Matchers\Floats\IsWithin;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(IsWithin::class)]
class IsWithinTest extends TestCase
{
    private const SmallDifference = 0.00000001;

    private const SmallRange = self::SmallDifference * 2.0;

    private const LargeDifference = 4270.42435525;

    private const LargeRange = self::LargeDifference * 2.0;

    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        foreach (DataFactory::floats() as $label => $args) {
            $value = $args[0];
            yield "{$label}-within-small-range" => [$value, $value + self::SmallRange, $value + self::SmallDifference];
        }
    }

    /** Ensure floats within the bounds with small ranges match successfully. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(float $lowerBound, float $upperBound, float $testValue): void
    {
        self::assertTrue((new IsWithin($lowerBound, $upperBound))->matches($testValue));
    }

    public static function dataForTestMatches2(): iterable
    {
        foreach (DataFactory::floats() as $label => $args) {
            $value = $args[0];
            yield "{$label}-within-large-range" => [$value, $value + self::LargeRange,  $value + self::LargeDifference];
        }
    }

    /** Ensure floats within the bounds with large ranges match successfully. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(float $lowerBound, float $upperBound, float $testValue): void
    {
        self::assertTrue((new IsWithin($lowerBound, $upperBound))->matches($testValue));
    }

    public static function dataForTestMatches3(): iterable
    {
        foreach (DataFactory::floats() as $label => $args) {
            $value = $args[0];
            yield "{$label}-within-large-range-small-difference-lower" => [$value, $value + self::LargeRange,  $value + self::SmallDifference];
            yield "{$label}-within-large-range-small-difference-upper" => [$value, $value + self::LargeRange,  $value + self::LargeRange - self::SmallDifference];
        }
    }

    /** Ensure floats within the bounds with large ranges and values close to the bound match successfully. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(float $lowerBound, float $upperBound, float $testValue): void
    {
        self::assertTrue((new IsWithin($lowerBound, $upperBound))->matches($testValue));
    }

    public static function dataForTestMatches4(): iterable
    {
        foreach (DataFactory::floats() as $label => $args) {
            $value = $args[0];
            yield "{$label}-within-large-range-equal-lower" => [$value, $value + self::LargeRange,  $value];
            yield "{$label}-within-large-range-equal-upper" => [$value, $value + self::LargeRange,  $value + self::LargeRange];
        }
    }

    /** Ensure floats on the bounds don't match. */
    #[DataProvider("dataForTestMatches4")]
    public function testMatches4(float $lowerBound, float $upperBound, float $testValue): void
    {
        self::assertFalse((new IsWithin($lowerBound, $upperBound))->matches($testValue));
    }
    
    public static function dataForTestMatches5(): iterable
    {
        foreach (DataFactory::floats() as $label => $args) {
            $value = $args[0];
            yield "{$label}-less-than-lower-bound-small-difference" => [$value, $value + self::SmallRange, $value - self::SmallDifference];
            yield "{$label}-greater-than-upper-bound-small-difference" => [$value, $value + self::SmallRange, $value + self::SmallRange + self::SmallDifference];
        }
    }

    /** Ensure floats less than the lower bound or greater than the upper bound by a small quantity don't match. */
    #[DataProvider("dataForTestMatches5")]
    public function testMatches5(float $lowerBound, float $upperBound, float $testValue): void
    {
        self::assertFalse((new IsWithin($lowerBound, $upperBound))->matches($testValue));
    }
    
    public static function dataForTestMatches6(): iterable
    {
        foreach (DataFactory::floats() as $label => $args) {
            $value = $args[0];
            yield "{$label}-less-than-lower-bound-large-difference" => [$value, $value + self::LargeRange, $value - self::LargeDifference];
            yield "{$label}-greater-than-upper-bound-large-difference" => [$value, $value + self::LargeRange, $value + self::LargeRange + self::LargeDifference];
        }
    }

    /** Ensure floats less than the lower bound or greater than the upper bound by a large quantity don't match. */
    #[DataProvider("dataForTestMatches6")]
    public function testMatches6(float $lowerBound, float $upperBound, float $testValue): void
    {
        self::assertFalse((new IsWithin($lowerBound, $upperBound))->matches($testValue));
    }

    public static function dataForTestMatches7(): iterable
    {
        foreach (["small" => self::SmallRange, "large" => self::LargeRange] as $rangeLabel => $rangeSize) {
            foreach ([
                         DataFactory::singleWordStrings(),
                         DataFactory::singleCharacterStrings(),
                         DataFactory::floatStrings(),
                         DataFactory::arrays(),
                         DataFactory::positiveIntegers(20),
                         DataFactory::negativeIntegers(-20),
                         DataFactory::booleans(),
                         DataFactory::objects(),
                         DataFactory::resources(),
                     ] as $dataSource) {
                foreach (DataFactory::matrix(DataFactory::floats(), $dataSource) as $label => $args) {
                    $args = [$args[0], $args[0] + $rangeSize, $args[1]];
                    yield "{$label}-{$rangeLabel}-range" => $args;
                }
            }
        }
    }

    /** Ensure a reasonable subset of non-floats don't match. */
    #[DataProvider("dataForTestMatches7")]
    public function testMatches7(float $lowerBound, float $upperBound, mixed $value): void
    {
        self::assertFalse((new IsWithin($lowerBound, $upperBound))->matches($value));
    }

    public static function dataForTestDescribe1(): iterable
    {
        yield "zero-one" => [0.0, 1.0, "(float) > 0.0 && < 1.0"];
        yield "negative-integral-float" => [-2.0, 0.0, "(float) > -2.0 && < 0.0"];
        yield "positive-integral-float" => [5.0, 101, "(float) > 5.0 && < 101.0"];
        yield "negative-float" => [-7.5812000, 2.16832, "(float) > -7.5812 && < 2.16832"];
        yield "positive-float" => [3.1415926, 10.6226101, "(float) > 3.1415926 && < 10.6226101"];
    }

    /** Ensure the matcher describes itself as expected. */
    #[DataProvider("dataForTestDescribe1")]
    public function testDescribe1(float $lowerBound, float $upperBound, string $expected): void
    {
        self::assertSame($expected, (new IsWithin($lowerBound, $upperBound))->describe(self::nullSerialiser()));
    }
}
