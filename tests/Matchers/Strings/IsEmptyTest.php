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

namespace MokkdTests\Matchers\Strings;

use Mokkd\Matchers\Strings\IsEmpty;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IsEmptyTest extends TestCase
{
    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::emptyString();
    }

    /** Ensure an empty string matches successfully. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(string $string): void
    {
        self::assertTrue((new IsEmpty())->matches($string));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield from DataFactory::nonEmptyStrings();
    }

    /** Ensure a reasonable subset of non-empty strings don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(string $string): void
    {
        self::assertFalse((new IsEmpty())->matches($string));
    }

    public static function dataForTestMatches3(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::arrays();
        yield from DataFactory::integers();
        yield from DataFactory::floats();
        yield from DataFactory::booleans();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
    }

    /** Ensure a reasonable subset of non-strings don't match. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(mixed $string): void
    {
        self::assertFalse((new IsEmpty())->matches($string));
    }

    /** Ensure the matcher describes itself as expected. */
    public static function testDescribe1(): void
    {
        self::assertSame("(string) {empty}", (new IsEmpty())->describe(self::nullSerialiser()));
    }
}
