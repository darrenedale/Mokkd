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

use Mokkd\Matchers\Strings\BeginsWith;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsBeginningWithTest extends TestCase
{
    use CreatesNullSerialiser;

    private const Prefixes = [
        "prefix-empty" => [""],
        "prefix-whitespace" => ["  "],
        "prefix-alpha-lower" => ["def"],
        "prefix-alpha-upper" => ["DEF"],
        "prefix-numeric" => ["456"],
        "prefix-punctuation" => ["-!="],
        "prefix-mixed" => [" 1Mok!"],
    ];

    public static function dataForTestPrefix1(): iterable
    {
        yield from self::Prefixes;
    }

    #[DataProvider("dataForTestPrefix1")]
    public function testPrefix1(string $prefix): void
    {
        self::assertSame($prefix, (new BeginsWith($prefix))->prefix());
    }

    public static function dataForTestMatches1(): iterable
    {
        yield "string-begins-with-empty-string-empty-string" => ["", ""];
        yield "string-begins-with-empty-string-non-empty-string" => ["", "mokkd"];
        yield "string-begins-with-whitespace-string" => ["  ", "  mokkd"];
        yield "string-begins-with-alpha-lower" => ["abc", "abc-mokkd"];
        yield "string-begins-with-alpha-upper" => ["ABC", "ABC-mokkd"];
        yield "string-begins-with-numeric" => ["123", "123-mokkd"];
        yield "string-begins-with-punctuation" => [",-!", ",-!-mokkd"];
        yield "string-begins-with-mixed" => [" 1Mok!", " 1Mok!mokkd"];
    }

    /** Ensure a reasonable subset of prefixed strings match successfully. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(string $prefix, string $string): void
    {
        self::assertTrue((new BeginsWith($prefix))->matches($string));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "string-begins-with-whitespace-empty-string" => [" ", ""];
        yield "string-begins-with-whitespace-non-empty-string" => [" ", "mokkd"];
        yield "string-begins-with-whitespace-insufficient-whitespace" => ["  ", " mokkd"];
        yield "string-begins-with-alpha-lower-upper" => ["abc", "ABC-mokkd"];
        yield "string-begins-with-alpha-upper-lower" => ["ABC", "abc-mokkd"];
        yield "string-begins-with-mixed-truncated-match" => ["ABC", "AB-C-mokkd"];
        yield "string-begins-with-mixed-whitespace" => [" 1Mok!", "  1Mok!-mokkd"];
        yield "string-begins-with-mixed-null-byte" => [" 1Mok!", "\0 1Mok!-mokkd"];
        yield "string-begins-with-mixed-wrong-end" => [" 1Mok!", "mokkd- 1Mok!"];
    }

    /** Ensure strings without the required prefix don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(string $prefix, string $string): void
    {
        self::assertFalse((new BeginsWith($prefix))->matches($string));
    }

    public static function dataForTestMatches3(): iterable
    {
        yield from DataFactory::matrix(self::Prefixes, ["null" => [null]]);
        yield from DataFactory::matrix(self::Prefixes, DataFactory::arrays());
        yield from DataFactory::matrix(self::Prefixes, DataFactory::integers());
        yield from DataFactory::matrix(self::Prefixes, DataFactory::floats());
        yield from DataFactory::matrix(self::Prefixes, DataFactory::booleans());
        yield from DataFactory::matrix(self::Prefixes, DataFactory::objects());
        yield from DataFactory::matrix(self::Prefixes, DataFactory::resources());
    }

    /** Ensure a reasonable subset of non-strings don't match. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(string $prefix, mixed $string): void
    {
        self::assertFalse((new BeginsWith($prefix))->matches($string));
    }

    public static function dataForTestDescribe1(): iterable
    {
        return self::Prefixes;
    }

    /** Ensure the matcher describes itself using the provided prefix. */
    #[DataProvider("dataForTestDescribe1")]
    public static function testDescribe1(string $prefix): void
    {
        self::assertSame("(string) \"{$prefix}…\"", (new BeginsWith($prefix))->describe(self::nullSerialiser()));
    }

    /** Ensure the matcher escapes double-quotes in the provided prefix. */
    public static function testDescribe2(): void
    {
        self::assertSame("(string) \"double-\\\"quoted\\\"-prefix…\"", (new BeginsWith("double-\"quoted\"-prefix"))->describe(self::nullSerialiser()));
    }
}
