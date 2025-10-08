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

use Mokkd\Matchers\Integers\IsEqualTo;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(IsEqualTo::class)]
class IsEqualToTest extends TestCase
{
    use CreatesNullSerialiser;

    private const SmallDifference = 1;

    private const LargeDifference = 5000;

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::combine(DataFactory::integers(), DataFactory::integers());
    }

    /** Ensure ints equal to the constraint match successfully. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(int $constraint, int $testValue): void
    {
        self::assertTrue((new IsEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches2(): iterable
    {
        foreach ([...DataFactory::negativeIntegers(), ...DataFactory::positiveIntegers(), ...DataFactory::integerZero()] as $label => $args) {
            $value = $args[0];
            yield "{$label}-greater-than-large-difference" => [$value, $value + self::LargeDifference];
            yield "{$label}-less-than-large-difference" => [$value, $value - self::LargeDifference];
        }
    }

    /** Ensure ints that differ from the constraint value by a large quantity don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(int $constraint, int $testValue): void
    {
        self::assertFalse((new IsEqualTo($constraint))->matches($testValue));
    }

    public static function dataForTestMatches3(): iterable
    {
        foreach ([...DataFactory::negativeIntegers(), ...DataFactory::positiveIntegers(), ...DataFactory::integerZero()] as $label => $args) {
            $value = $args[0];
            yield "{$label}-greater-than-small-difference" => [$value, $value + self::SmallDifference];
            yield "{$label}-less-than-small-difference" => [$value, $value - self::SmallDifference];
        }
    }

    /** Ensure ints that differ from the constraint value by a small quantity don't match. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(int $constraint, int $testValue): void
    {
        self::assertFalse((new IsEqualTo($constraint))->matches($testValue));
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
        self::assertFalse((new IsEqualTo($constraint))->matches($value));
    }

    public static function dataForTestDescribe1(): iterable
    {
        yield "zero" => [0, "(int) == 0"];
        yield "negative-int" => [-7, "(int) == -7"];
        yield "positive-int" => [42, "(int) == 42"];
    }

    /** Ensure the matcher describes itself as expected. */
    #[DataProvider("dataForTestDescribe1")]
    public function testDescribe1(int $constraint, string $expected): void
    {
        self::assertSame($expected, (new IsEqualTo($constraint))->describe(self::nullSerialiser()));
    }
}
