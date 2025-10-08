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

use Mokkd\Matchers\Integers\IsNotZero;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(IsNotZero::class)]
class IsNotZeroTest extends TestCase
{
    use CreatesNullSerialiser;

    /** Ensure a zero int does not match. */
    public function testMatches1(): void
    {
        self::assertFalse((new IsNotZero())->matches(0));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield from DataFactory::negativeIntegers();
        yield from DataFactory::positiveIntegers();
    }

    /** Ensure a reasonable subset of non-zero ints match successfully. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(int $value): void
    {
        self::assertTrue((new IsNotZero())->matches($value));
    }

    /** Ensure a zero float does not match. */
    public function testMatches3(): void
    {
        self::assertFalse((new IsNotZero())->matches(0.0));
    }

    public static function dataForTestMatches4(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::singleWordStrings();
        yield from DataFactory::singleCharacterStrings();
        yield from DataFactory::integerStrings(-20, 20);
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
        self::assertFalse((new IsNotZero())->matches($value));
    }

    /** Ensure the matcher describes itself as expected. */
    public static function testDescribe1(): void
    {
        self::assertSame("(int) != 0", (new IsNotZero())->describe(self::nullSerialiser()));
    }
}
