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

namespace Matchers\Strings;

use Equit\XRay\XRay;
use Mokkd\Matchers\Strings\DoesNotMatchPattern;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class DoesNotMatchPatternTest extends TestCase
{
    use CreatesNullSerialiser;

    /** Ensure the default character encoding is UTF-8. */
    public function testConstructor1(): void
    {
        $matcher = new DoesNotMatchPattern(".*");
        self::assertSame("UTF-8", (new XRay($matcher))->encoding);
    }

    /** Ensure matching is case-sensitive by default. */
    public function testConstructor2(): void
    {
        self::assertTrue((new DoesNotMatchPattern("^ABC\$"))->matches("ABc"));
    }

    public static function dataForTestMatches1(): iterable
    {
        yield "empty-pattern-empty-string" => ["^\$", ""];
        yield "whitespace-pattern-whitespace-string" => ["^  \$", "  "];
        yield "anything-pattern-non-empty-string" => [".*", "Mokkd"];
        yield "anything-pattern-empty-string" => [".*", ""];
        yield "prefix-pattern-prefixed-string" => ["^Mokkd", "Mokkd "];
        yield "suffix-pattern-suffixed-string" => ["Mokkd\$", " Mokkd"];
        yield "infix-pattern-infixed-string" => ["Mokkd", " Mokkd "];
    }

    /** Ensure a reasonable subset of strings matching the regular expression don't match the Matcher. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(string $pattern, string $string): void
    {
        self::assertFalse((new DoesNotMatchPattern($pattern))->matches($string));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "empty-pattern-non-empty-string" => ["^\$", "Mokkd"];
        yield "whitespace-pattern-whitespace-string" => ["^  \$", "   "];
        yield "prefix-pattern-non-prefixed-string" => ["^Mokkd", " Mokkd "];
        yield "suffix-pattern-non-suffixed-string" => ["Mokkd\$", " Mokkd "];
        yield "infix-pattern-non-infixed-string" => ["Mokkd", " Mokd "];
    }

    /** Ensure strings that don't match the pattern match the Matcher successfully. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(string $pattern, string $string): void
    {
        self::assertTrue((new DoesNotMatchPattern($pattern))->matches($string));
    }

    public static function dataForTestMatches3(): iterable
    {
        $patterns = [
            "empty-pattern" => ["^\$"],
            "anything-pattern" => [".*"],
        ];

        yield from DataFactory::matrix($patterns, ["null" => [null]]);
        yield from DataFactory::matrix($patterns, DataFactory::arrays());
        yield from DataFactory::matrix($patterns, DataFactory::integers());
        yield from DataFactory::matrix($patterns, DataFactory::floats());
        yield from DataFactory::matrix($patterns, DataFactory::booleans());
        yield from DataFactory::matrix($patterns, DataFactory::objects());
        yield from DataFactory::matrix($patterns, DataFactory::resources());
    }

    /** Ensure a reasonable subset of non-strings don't match. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(string $pattern, mixed $string): void
    {
        self::assertFalse((new DoesNotMatchPattern($pattern))->matches($string));
    }

    /** Ensure the equivalent string in the wrong character encoding does not match the regular expression and therefore
     * matches the Matcher successfully.
     */
    public function testMatches5(): void
    {
        // ^Mökkd$ in UTF-16
        $matcher = new DoesNotMatchPattern("\x00\x5e\x00\x4d\x00\xf6\x00\x6b\x00\x6b\x00\x64\x00\x24", "UTF-16");
        self::assertTrue($matcher->matches("Mökkd"));
    }

    /** Ensure case-insensitive matching can be performed. */
    public function testMatches6(): void
    {
        self::assertFalse((new DoesNotMatchPattern("^Mokkd\$", caseSensitive: false))->matches("MOKKD"));
    }

    public static function dataForTestDescribe1(): iterable
    {
        yield "empty-pattern-utf-8" => ["^\$", "UTF-8"];
        yield "anything-pattern-utf-16" => ["\x00\x2e\x00\x2a", "UTF-16"];
    }

    /** Ensure the matcher describes itself using the provided encoding and pattern. */
    #[DataProvider("dataForTestDescribe1")]
    public static function testDescribe1(string $pattern, string $encoding): void
    {
    self::assertSame("({$encoding}-string) !~= {$pattern}", (new DoesNotMatchPattern($pattern, $encoding))->describe(self::nullSerialiser()));
    }
}
