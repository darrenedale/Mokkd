<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Strings;

use LogicException;
use Mokkd\Matchers\Strings\IsStringLongerThan;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\Matchers\RelabelMode;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/** TODO tests with different encodings. */
class IsStringLongerThanTest extends TestCase
{
    use CreatesNullSerialiser;

    private const Lengths = [
        "zero" => [0],
        "one" => [1],
        "two" => [2],
        "hundred" => [100],
    ];

    private const Encodings = [
        "utf-8" => ["UTF-8"],
        "utf-16" => ["UTF-16"],
        "utf-32" => ["UTF-32"],
        "ucs-32" => ["UCS-2"],
        "iso-8859-1" => ["ISO-8859-1"],
        "iso-8859-2" => ["ISO-8859-2"],
        "iso-8859-3" => ["ISO-8859-3"],
        "iso-8859-4" => ["ISO-8859-4"],
        "iso-8859-5" => ["ISO-8859-5"],
        "iso-8859-6" => ["ISO-8859-6"],
        "iso-8859-7" => ["ISO-8859-7"],
        "iso-8859-8" => ["ISO-8859-8"],
        "iso-8859-9" => ["ISO-8859-9"],
        "iso-8859-10" => ["ISO-8859-10"],
        "iso-8859-13" => ["ISO-8859-13"],
        "iso-8859-14" => ["ISO-8859-14"],
        "iso-8859-15" => ["ISO-8859-15"],
        "iso-8859-16" => ["ISO-8859-16"],
    ];

    public static function dataForTestConstructor1(): iterable
    {
        yield from DataFactory::negativeIntegers();
        yield from DataFactory::minInteger();
    }

    /** Ensure the expected LogicException is thrown if the constructor receives a length < 0 */
    #[DataProvider("dataForTestConstructor1")]
    public function testConstructor1(int $length): void
    {
        self::skipIfAssertionsDisabled();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Expecting length >= 0, found {$length}");
        new IsStringLongerThan($length);
    }

    /** Ensure encoding is UTF-8 by default. */
    public function testConstructor2(): void
    {
        self::assertSame("UTF-8", (new IsStringLongerThan(10))->encoding());
    }

    public static function dataForTestLength1(): iterable
    {
        yield from DataFactory::integerZero();
        yield from DataFactory::positiveIntegers();
        yield from DataFactory::maxInteger();
    }

    /** Ensure the constructor sets the length and we can retrieve it. */
    #[DataProvider("dataForTestLength1")]
    public function testLength1(int $length): void
    {
        self::assertSame($length, (new IsStringLongerThan($length))->length());
    }

    public static function dataForTestEncoding1(): iterable
    {
        return self::Encodings;
    }

    /** Ensure the constructor sets the length and we can retrieve it. */
    #[DataProvider("dataForTestEncoding1")]
    public function testEncoding1(string $encoding): void
    {
        self::assertSame($encoding, (new IsStringLongerThan(10, $encoding))->encoding());
    }

    public static function dataForTestMatches1(): iterable
    {
        yield "string-longer-than-zero-character" => [0, "a"];
        yield "string-longer-than-zero-string" => [0, "mokkd"];

        foreach (DataFactory::positiveIntegers() as $length) {
            $length = DataFactory::unboxSingle($length);
            yield "string-longer-than-{$length}-string-plus-one" => [$length, str_repeat("m", $length + 1)];
            yield "string-longer-than-{$length}-string-plus-10" => [$length, str_repeat("k", $length + 10)];
            yield "string-longer-than-{$length}-multibyte-characters" => [$length, str_repeat("é", $length + 10)];
        }
    }

    /** Ensure a reasonable subset of longer strings match successfully. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(int $length, string $string): void
    {
        self::assertTrue((new IsStringLongerThan($length))->matches($string));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "string-longer-than-zero-empty" => [0, ""];

        foreach (DataFactory::positiveIntegers() as $length) {
            $length = DataFactory::unboxSingle($length);
            yield "string-longer-than-{$length}-string-equal-length" => [$length, str_repeat("m", $length)];
            yield "string-longer-than-{$length}-string-minus-one" => [$length, str_repeat("m", $length - 1)];
            yield "string-longer-than-{$length}-empty" => [$length, ""];
        }

        yield "string-longer-than-10-more-bytes-fewer-characters" => [10, str_repeat("é", 6)];
    }

    /** Ensure a reasonable subset of shorter (or same length) strings fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(int $length, string $string): void
    {
        self::assertFalse((new IsStringLongerThan($length))->matches($string));
    }

    public static function dataForTestMatches3(): iterable
    {
        yield from DataFactory::matrix(self::Lengths, ["null" => [null]]);
        yield from DataFactory::matrix(self::Lengths, DataFactory::arrays());
        yield from DataFactory::matrix(self::Lengths, DataFactory::integers());
        yield from DataFactory::matrix(self::Lengths, DataFactory::floats());
        yield from DataFactory::matrix(self::Lengths, DataFactory::booleans());
        yield from DataFactory::matrix(self::Lengths, DataFactory::objects());
        yield from DataFactory::matrix(self::Lengths, DataFactory::resources());
    }

    /** Ensure a reasonable subset of non-strings don't match. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(int $length, mixed $string): void
    {
        self::assertFalse((new IsStringLongerThan($length))->matches($string));
    }

    public static function dataForTestDescribe1(): iterable
    {
        yield from DataFactory::relabel(DataFactory::matrix(self::Lengths, self::Encodings), "string-longer-than-", RelabelMode::Prefix);
    }

    /** Ensure the matcher describes itself correctly according to the length and encoding. */
    #[DataProvider("dataForTestDescribe1")]
    public static function testDescribe1(int $length, string $encoding): void
    {
        self::assertSame("({$encoding}-string[>{$length}])", (new IsStringLongerThan($length, $encoding))->describe(self::nullSerialiser()));
    }

    /** Ensure the matcher describes itself with UTF-8 encoding by default. */
    public static function testDescribe2(): void
    {
        self::assertSame("(UTF-8-string[>10])", (new IsStringLongerThan(10))->describe(self::nullSerialiser()));
    }
}
