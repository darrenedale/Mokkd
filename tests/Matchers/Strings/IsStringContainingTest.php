<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Strings;

use Mokkd\Matchers\Strings\IsStringContaining;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsStringContainingTest extends TestCase
{
    use CreatesNullSerialiser;

    private const Infixes = [
        "infix-empty" => [""],
        "infix-whitespace" => ["  "],
        "infix-alpha-lower" => ["def"],
        "infix-alpha-upper" => ["DEF"],
        "infix-numeric" => ["456"],
        "infix-punctuation" => ["-!="],
        "infix-mixed" => [" 1Mok!"],
    ];

    public static function dataForTestInfix1(): iterable
    {
        yield from self::Infixes;
    }

    #[DataProvider("dataForTestInfix1")]
    public function testInfix1(string $infix): void
    {
        self::assertSame($infix, (new IsStringContaining($infix))->infix());
    }

    public static function dataForTestMatches1(): iterable
    {
        yield "string-contains-empty-string-empty-string" => ["", ""];
        yield "string-contains-empty-string-non-empty-string" => ["", "mokkd"];
        yield "string-contains-whitespace-string-prefix" => ["  ", "  mokkd"];
        yield "string-contains-whitespace-string-infix" => ["  ", "mok  kd"];
        yield "string-contains-whitespace-string-suffix" => ["  ", "mokkd  "];
        yield "string-contains-alpha-lower-prefix" => ["jkl", "jkl-mokkd"];
        yield "string-contains-alpha-lower-infix" => ["jkl", "mok-jkl-kd"];
        yield "string-contains-alpha-lower-suffix" => ["jkl", "jkl-mokkd"];
        yield "string-contains-alpha-upper-prefix" => ["ZYX", "ZYX-mokkd"];
        yield "string-contains-alpha-upper-infix" => ["ZYX", "mZYXokkd"];
        yield "string-contains-alpha-upper-suffix" => ["ZYX", "mokkd-ZYX"];
        yield "string-contains-numeric-prefix" => ["432", "432mokkd"];
        yield "string-contains-numeric-infix" => ["432", "mokk432d"];
        yield "string-contains-numeric-suffix" => ["432", "mokkd432"];
        yield "string-contains-punctuation-prefix" => [",-!", ",-!mokkd"];
        yield "string-contains-punctuation-infix" => [",-!", "mo,-!kkd"];
        yield "string-contains-punctuation-suffix" => [",-!", "mokkd,-!"];
        yield "string-contains-mixed-prefix" => [" 1Mok!", " 1Mok!mokkd"];
        yield "string-contains-mixed-infix" => [" 1Mok!", "mokk 1Mok!d"];
        yield "string-contains-mixed-suffix" => [" 1Mok!", "mokkd 1Mok!"];
    }

    /** Ensure a reasonable subset of infixed strings match successfully. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(string $infix, string $string): void
    {
        self::assertTrue((new IsStringContaining($infix))->matches($string));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "string-contains-whitespace-empty-string" => [" ", ""];
        yield "string-contains-whitespace-non-empty-string" => [" ", "mokkd"];
        yield "string-contains-whitespace-insufficient-whitespace-prefix" => ["  ", " mokkd"];
        yield "string-contains-whitespace-insufficient-whitespace-infix" => ["  ", "mo kkd"];
        yield "string-contains-whitespace-insufficient-whitespace-suffix" => ["  ", "mokkd "];
        yield "string-contains-alpha-lower-upper-prefix" => ["zyx", "ZYX-mokkd"];
        yield "string-contains-alpha-lower-upper-infix" => ["zyx", "moZYXkkd"];
        yield "string-contains-alpha-lower-upper-suffix" => ["zyx", "mokkd-ZYX"];
        yield "string-contains-alpha-upper-lower-prefix" => ["HJL", "hjl-mokkd"];
        yield "string-contains-alpha-upper-lower-infix" => ["HJL", "m-hjl-okkd"];
        yield "string-contains-alpha-upper-lower-suffix" => ["HJL", "mokkdhjl"];
        yield "string-contains-mixed-truncated-match-prefix" => ["EFG", "EFmokkd"];
        yield "string-contains-mixed-truncated-match-infix" => ["EFG", "mFGokkd"];
        yield "string-contains-mixed-truncated-match-suffix" => ["EFG", "mokkd-E-FG"];
    }

    /** Ensure strings without the required infix don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(string $infix, string $string): void
    {
        self::assertFalse((new IsStringContaining($infix))->matches($string));
    }

    public static function dataForTestMatches3(): iterable
    {
        yield from DataFactory::matrix(self::Infixes, ["null" => [null]]);
        yield from DataFactory::matrix(self::Infixes, DataFactory::arrays());
        yield from DataFactory::matrix(self::Infixes, DataFactory::integers());
        yield from DataFactory::matrix(self::Infixes, DataFactory::floats());
        yield from DataFactory::matrix(self::Infixes, DataFactory::booleans());
        yield from DataFactory::matrix(self::Infixes, DataFactory::objects());
        yield from DataFactory::matrix(self::Infixes, DataFactory::resources());
    }

    /** Ensure a reasonable subset of non-strings don't match. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(string $infix, mixed $string): void
    {
        self::assertFalse((new IsStringContaining($infix))->matches($string));
    }

    public static function dataForTestDescribe1(): iterable
    {
        return self::Infixes;
    }

    /** Ensure the matcher describes itself using the provided infix. */
    #[DataProvider("dataForTestDescribe1")]
    public static function testDescribe1(string $infix): void
    {
        self::assertSame("(string) \"…{$infix}…\"", (new IsStringContaining($infix))->describe(self::nullSerialiser()));
    }

    /** Ensure the matcher escapes double-quotes in the provided infix. */
    public static function testDescribe2(): void
    {
        self::assertSame("(string) \"…double-\\\"quoted\\\"-infix…\"", (new IsStringContaining("double-\"quoted\"-infix"))->describe(self::nullSerialiser()));
    }
}
