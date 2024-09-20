<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Strings;

use Mokkd\Matchers\Strings\IsStringNotBeginningWith;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsStringNotBeginningWithTest extends TestCase
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

    public static function dataForTestMatches1(): iterable
    {
        foreach (DataFactory::singleCharacterLowerCaseStrings() as $label => $ch) {
            $ch = DataFactory::unboxSingle($ch);
            yield "string-not-begins-with-{$label}-empty-string" => [$ch, ""];
            yield "string-not-begins-with-{$label}-leading-whitespace" => [$ch, " {$ch}"];
            yield "string-not-begins-with-{$label}-leading-null-byte" => [$ch, "\0{$ch}"];
            yield "string-not-begins-with-{$label}-upper-case" => [$ch, strtoupper($ch)];
        }

        foreach (DataFactory::singleCharacterUpperCaseStrings() as $label => $ch) {
            $ch = DataFactory::unboxSingle($ch);
            yield "string-not-begins-with-{$label}-empty-string" => [$ch, ""];
            yield "string-not-begins-with-{$label}-leading-whitespace" => [$ch, " {$ch}"];
            yield "string-not-begins-with-{$label}-leading-null-byte" => [$ch, "\0{$ch}"];
            yield "string-not-begins-with-{$label}-lower-case" => [$ch, strtolower($ch)];
        }

        foreach (DataFactory::singleWordLowerCaseStrings() as $label => $word) {
            $word = DataFactory::unboxSingle($word);
            yield "string-not-begins-with-{$label}-empty-string" => [$word, ""];
            yield "string-not-begins-with-{$label}-leading-whitespace" => [$word, " {$word}"];
            yield "string-not-begins-with-{$label}-leading-null-byte" => [$word, "\0{$word}"];
            yield "string-not-begins-with-{$label}-upper-case" => [$word, strtoupper($word)];
        }

        foreach (DataFactory::singleWordUpperCaseStrings() as $label => $word) {
            $word = DataFactory::unboxSingle($word);
            yield "string-not-begins-with-{$label}-empty-string" => [$word, ""];
            yield "string-not-begins-with-{$label}-leading-whitespace" => [$word, " {$word}"];
            yield "string-not-begins-with-{$label}-leading-null-byte" => [$word, "\0{$word}"];
            yield "string-not-begins-with-{$label}-lower-case" => [$word, strtolower($word)];
        }
    }

    /** Ensure a reasonable subset of un-prefixed strings match successfully. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(string $prefix, string $string): void
    {
        self::assertTrue((new IsStringNotBeginningWith($prefix))->matches($string));
    }

    public static function dataForTestMatches2(): iterable
    {
        foreach (DataFactory::singleCharacterStrings() as $label => $ch) {
            $ch = DataFactory::unboxSingle($ch);
            yield "string-not-begins-with-{$label}-{$label}" => [$ch, $ch];
            yield "string-not-begins-with-{$label}-empty-string" => ["", $ch];
        }

        foreach (DataFactory::singleWordStrings() as $label => $word) {
            $word = DataFactory::unboxSingle($word);
            yield "string-not-begins-with-{$label}-{$label}" => [$word, $word];
            yield "string-not-begins-with-{$label}-empty-string" => ["", $word];
            yield "string-not-begins-with-{$label}-first-char" => [$word[0], $word];
            yield "string-not-begins-with-{$label}-all-except-last-char" => [substr($word, 0, -1), $word];
        }

        foreach (DataFactory::multiWordStrings() as $label => $words) {
            $words = DataFactory::unboxSingle($words);
            yield "string-not-begins-with-{$label}-{$label}" => [$words, $words];
            yield "string-not-begins-with-{$label}-empty-string" => ["", $words];
            yield "string-not-begins-with-{$label}-first-char" => [$words[0], $words];
            yield "string-not-begins-with-{$label}-all-except-last-char" => [substr($words, 0, -1), $words];
        }
    }

    /** Ensure strings with the prefix don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(string $prefix, string $string): void
    {
        self::assertFalse((new IsStringNotBeginningWith($prefix))->matches($string));
    }

    public static function dataForTestMatches3(): iterable
    {
        yield "string-not-begins-with-null" => [null];
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
        self::assertFalse((new IsStringNotBeginningWith(""))->matches($string));
    }

    public static function dataForTestDescribe1(): iterable
    {
        return self::Prefixes;
    }

    /** Ensure the matcher describes itself using the provided prefix. */
    #[DataProvider("dataForTestDescribe1")]
    public static function testDescribe1(string $prefix): void
    {
        self::assertSame("(string) !\"{$prefix}…\"", (new IsStringNotBeginningWith($prefix))->describe(self::nullSerialiser()));
    }

    /** Ensure the matcher escapes double-quotes in the provided prefix. */
    public static function testDescribe2(): void
    {
        self::assertSame("(string) !\"double-\\\"quoted\\\"-prefix…\"", (new IsStringNotBeginningWith("double-\"quoted\"-prefix"))->describe(self::nullSerialiser()));
    }
}
