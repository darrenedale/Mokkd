<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Strings;

use Mokkd\Matchers\Strings\IsJson;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IsJsonStringTest extends TestCase
{
    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::jsonStrings();
        yield from DataFactory::integerStrings();
        yield from DataFactory::floatStrings();
    }

    /** Ensure JSON strings match successfully. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(string $string): void
    {
        self::assertTrue((new IsJson())->matches($string));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield from DataFactory::emptyString();
        yield from DataFactory::whitespaceString();
        yield from DataFactory::singleCharacterStrings();
        yield from DataFactory::singleWordStrings();
        yield from DataFactory::multiWordStrings();
        yield from DataFactory::binaryStrings();
    }

    /** Ensure a reasonable subset of non-JSON strings don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(string $string): void
    {
        self::assertFalse((new IsJson())->matches($string));
    }

    public static function dataForTestMatches3(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::arrays();
        yield from DataFactory::whitespaceString();
        yield from DataFactory::singleCharacterStrings();
        yield from DataFactory::singleWordStrings();
        yield from DataFactory::multiWordStrings();
        yield from DataFactory::binaryStrings();
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
        self::assertFalse((new IsJson())->matches($string));
    }

    /** Ensure the matcher describes itself as expected. */
    public static function testDescribe1(): void
    {
        self::assertSame("(string) {json}", (new IsJson())->describe(self::nullSerialiser()));
    }
}
