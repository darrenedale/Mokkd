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

namespace MokkdTests\Matchers\Numerics;

use Mokkd\Matchers\Numerics\IsNotZero;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(IsNotZero::class)]
class IsNotZeroTest extends TestCase
{
    use CreatesNullSerialiser;

    private const SmallFloatDifference = 0.00000001;

    private const LargeFloatDifference = 4270.42435525;


    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::integerZero();
        yield from DataFactory::floatZero();
    }

    /** Ensure numerics equal to zero don't match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(int|float $testValue): void
    {
        self::assertFalse((new IsNotZero())->matches($testValue));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield from DataFactory::negativeIntegers(-20);
        yield from DataFactory::positiveIntegers(20);
    }

    /** Ensure ints that differ from zero match successfully. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(int $testValue): void
    {
        self::assertTrue((new IsNotZero())->matches($testValue));
    }

    public static function dataForTestMatches3(): iterable
    {
        yield from DataFactory::negativeFloats();
        yield from DataFactory::positiveFloats();
        yield "float-greater-than-small-difference" => self::SmallFloatDifference;
        yield "float-less-than-small-difference" => -self::SmallFloatDifference;
        yield "float-greater-than-large-difference" => self::LargeFloatDifference;
        yield "float-less-than-large-difference" => -self::LargeFloatDifference;
    }

    /** Ensure floats that differ from a float constraint value by a large quantity match successfully. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(float $testValue): void
    {
        self::assertTrue((new IsNotZero())->matches($testValue));
    }

    public static function dataForTestMatches4(): iterable
    {
        yield from DataFactory::nullValue();
        yield from DataFactory::singleWordStrings();
        yield from DataFactory::singleCharacterStrings();
        yield from DataFactory::integerStrings(-20, 20);
        yield from DataFactory::floatStrings();
        yield from DataFactory::arrays();
        yield from DataFactory::booleans();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
        yield from DataFactory::nullValue();
        yield from DataFactory::singleWordStrings();
        yield from DataFactory::singleCharacterStrings();
        yield from DataFactory::integerStrings(-20, 20);
        yield from DataFactory::floatStrings();
        yield from DataFactory::arrays();
        yield from DataFactory::booleans();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
    }

    /** Ensure a reasonable subset of non-numerics don't match. */
    #[DataProvider("dataForTestMatches4")]
    public function testMatches4(mixed $value): void
    {
        self::assertFalse((new IsNotZero())->matches($value));
    }

    /** Ensure the matcher describes itself as expected. */
    public function testDescribe1(): void
    {
        self::assertSame("(int|float) != 0", (new IsNotZero())->describe(self::nullSerialiser()));
    }
}
