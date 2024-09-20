<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Strings;

use Mokkd\Matchers\Strings\IsStringEndingWith;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsStringEndingWithTest extends TestCase
{
    use CreatesNullSerialiser;

    private const Suffixes = [
        "suffix-empty" => [""],
        "suffix-whitespace" => ["  "],
        "suffix-alpha-lower" => ["def"],
        "suffix-alpha-upper" => ["DEF"],
        "suffix-numeric" => ["456"],
        "suffix-punctuation" => ["-!="],
        "suffix-mixed" => [" 1Mok!"],
    ];

    public static function dataForTestMatches1(): iterable
    {
        yield "string-ends-with-empty-string-empty-string" => ["", ""];
        yield "string-ends-with-empty-string-non-empty-string" => ["", "mokkd"];
        yield "string-ends-with-whitespace-string" => ["  ", "mokkd  "];
        yield "string-ends-with-alpha-lower" => ["jkl", "mokkd-jkl"];
        yield "string-ends-with-alpha-upper" => ["ZYX", "mokkd-ZYX"];
        yield "string-ends-with-numeric" => ["432", "mokkd-432"];
        yield "string-ends-with-punctuation" => [",-!", "mokkd,-!"];
        yield "string-ends-with-mixed" => [" 1Mok!", "mokkd 1Mok!"];
    }

    /** Ensure a reasonable subset of suffixed strings match successfully. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(string $suffix, string $string): void
    {
        self::assertTrue((new IsStringEndingWith($suffix))->matches($string));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "string-ends-with-whitespace-empty-string" => [" ", ""];
        yield "string-ends-with-whitespace-non-empty-string" => [" ", "mokkd"];
        yield "string-ends-with-whitespace-insufficient-whitespace" => ["  ", "mokkd "];
        yield "string-ends-with-alpha-lower-upper" => ["zyx", "mokkd-ZYX"];
        yield "string-ends-with-alpha-upper-lower" => ["HJL", "mokkd-hjl"];
        yield "string-ends-with-mixed-truncated-match" => ["EFG", "mokkd-E-FG"];
        yield "string-ends-with-mixed-whitespace" => [" 1Mok!", "mokkd- 1Mok! "];
        yield "string-ends-with-mixed-null-byte" => [" 1Mok!", "-mokkd 1Mok!\0"];
        yield "string-ends-with-mixed-wrong-end" => [" 1Mok!", " 1Mok!mokkd-"];
    }

    /** Ensure strings without the required suffix don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(string $suffix, string $string): void
    {
        self::assertFalse((new IsStringEndingWith($suffix))->matches($string));
    }

    public static function dataForTestMatches3(): iterable
    {
        yield from DataFactory::matrix(self::Suffixes, ["null" => [null]]);
        yield from DataFactory::matrix(self::Suffixes, DataFactory::arrays());
        yield from DataFactory::matrix(self::Suffixes, DataFactory::integers());
        yield from DataFactory::matrix(self::Suffixes, DataFactory::floats());
        yield from DataFactory::matrix(self::Suffixes, DataFactory::booleans());
        yield from DataFactory::matrix(self::Suffixes, DataFactory::objects());
        yield from DataFactory::matrix(self::Suffixes, DataFactory::resources());
    }

    /** Ensure a reasonable subset of non-strings don't match. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(string $suffix, mixed $string): void
    {
        self::assertFalse((new IsStringEndingWith($suffix))->matches($string));
    }

    public static function dataForTestDescribe1(): iterable
    {
        return self::Suffixes;
    }

    /** Ensure the matcher describes itself using the provided suffix. */
    #[DataProvider("dataForTestDescribe1")]
    public static function testDescribe1(string $suffix): void
    {
        self::assertSame("(string) \"…{$suffix}\"", (new IsStringEndingWith($suffix))->describe(self::nullSerialiser()));
    }

    /** Ensure the matcher escapes double-quotes in the provided suffix. */
    public static function testDescribe2(): void
    {
        self::assertSame("(string) \"…double-\\\"quoted\\\"-suffix\"", (new IsStringEndingWith("double-\"quoted\"-suffix"))->describe(self::nullSerialiser()));
    }
}
