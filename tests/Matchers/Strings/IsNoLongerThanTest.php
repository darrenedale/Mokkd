<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Strings;

use LogicException;
use Mokkd\Matchers\Strings\IsNoLongerThan;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\Matchers\RelabelMode;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests IsStringNoLongerThan matcher with various character encodings. Of those that the mbstring extension supports,
 * ASCII, all the ISO-8859 encodings, UTF-8, UTF-16 (BE, LE and auto-detected) and UTF-32 (BE, LE and auto-detected),
 * and Windows code pages 1251 and 1252 are tested. This should cover the vast majority of use-cases. KOI8-*, EUC-* and
 * BIG-5 may follow after some research.
 */
class IsNoLongerThanTest extends TestCase
{
    use CreatesNullSerialiser;

    private const Lengths = [
        "one" => [1],
        "two" => [2],
        "hundred" => [100],
    ];

    private const SingleByteEncodings = [
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
        // NOTE Thai not supported (yet?) by mbstring
        //"iso-8859-11" => ["ISO-8859-11"],
        "iso-8859-13" => ["ISO-8859-13"],
        "iso-8859-14" => ["ISO-8859-14"],
        "iso-8859-15" => ["ISO-8859-15"],
        "iso-8859-16" => ["ISO-8859-16"],
        "ascii" => ["ASCII"],
        "Windows-1251" => ["Windows-1251"],
        "Windows-1252" => ["Windows-1252"],
    ];

    private const MultiByteEncodings = [
        "utf-8" => ["UTF-8"],
        "utf-16" => ["UTF-16"],
        "utf-16le" => ["UTF-16LE"],
        "utf-16be" => ["UTF-16BE"],
        "utf-32" => ["UTF-32"],
        "utf-32le" => ["UTF-32LE"],
        "utf-32be" => ["UTF-32BE"],
    ];

    private const SingleByteEncodingStrings = [
        // Latin 1 - MøkkðFüñç
        "iso-8859-1" => ["\x4d\xf8\x6b\x6b\xf0\x46\xfc\xf1\xe7"],

        // W & C European - MőkkđŢěşť
        "iso-8859-2" => ["\x4d\xf5\x6b\x6b\xf0\xde\xec\xba\xbb"],

        // W & S European - ŜüġárĈäñè
        "iso-8859-3" => ["\xde\xfc\xf5\xe1\x72\xc6\xe4\xf1\xe8"],

        // W European & Baltic - MöķķđFųņč
        "iso-8859-4" => ["\x4d\xf6\xf3\xf3\xf0\x46\xf9\xf1\xe8"],

        // Cyrillic - АятФfСюдє
        "iso-8859-5" => ["\xb0\xef\xe2\xc4\x66\xc1\xee\xd4\xf4"],

        // Arabic - I know nothing about pronouncing Arabic, so just some letters
        // آ	e	ؤ	f	ئ	g	ب	h	ث
        "iso-8859-6" => ["\xc2\x65\xc4\x66\xc6\x67\xc8\x68\xcb"],

        // Greek - ΜοκκδΤεστ
        "iso-8859-7" => ["\xcc\xef\xea\xea\xe4\xd4\xe5\xf3\xf4"],

        // Hebrew - I know nothing about pronouncing Hebrew, so just some letters: אaבbגcתzש
        "iso-8859-8" => ["\xe0\x61\xe1\x62\xe2\x63\xfa\x7a\xf9"],

        // Turkish - ŞõÿåĞräîñ
        "iso-8859-9" => ["\xde\xf5\xff\xe5\xd0\x72\xe4\xee\xf1"],

        // Nordic and Icelandic - ĢļōāŧĶįņģ
        "iso-8859-10" => ["\xa3\xb8\xf2\xe0\xbb\xa6\xe7\xf1\xb3"],

        // Thai - I know nothing about pronouncing Thai, so just some letters: ฑqผyฎvกj๔
        // NOTE not supported (yet?) by mbstring
        //"iso-8859-11" => ["\xb1\x71\xbc\x79\xae\x76\xa1\x6a\xf4"],

        // Baltic and Polish - ŁōćąłŅōšė
        "iso-8859-13" => ["\xd9\xf4\xe3\xe0\xf9\xd2\xf4\xf0\xeb"],

        // Celtic - ṪiñŷẄínçë
        "iso-8859-14" => ["\xd7\x69\xf1\xfe\xbd\xed\x6e\xe7\xeb"],

        // Latin 1 plus EUR - L€mœñŽéšt
        "iso-8859-15" => ["\x4c\xa4\x6d\xbd\xf1\xb4\xe9\xa8\x74"],

        // C, E and S European - ßęśțŚőűńđ
        "iso-8859-16" => ["\xdf\xfd\xf7\xfe\xd7\xf5\xf8\xf1\xf0"],

        // MokkdFunc
        "ascii" => ["\x4d\x6f\x6b\x6b\x64\x46\x75\x6e\x63"],

        // Windows Cyrillic - АятФfСюдє
        "Windows-1251" => ["\xc0\xff\xf2\xd4\x66\xd1\xfe\xe4\xba"],

        // Windows Western - MøkkðFüñç
        "Windows-1252" => ["\x4d\xf8\x6b\x6b\xf0\x46\xfc\xf1\xe7"],
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
        new IsNoLongerThan($length);
    }

    /** Ensure encoding is UTF-8 by default. */
    public function testConstructor2(): void
    {
        self::assertSame("UTF-8", (new IsNoLongerThan(10))->encoding());
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
        self::assertSame($length, (new IsNoLongerThan($length))->length());
    }

    public static function dataForTestEncoding1(): iterable
    {
        yield from self::SingleByteEncodings;
        yield from self::MultiByteEncodings;
    }

    /** Ensure the constructor sets the encoding and we can retrieve it. */
    #[DataProvider("dataForTestEncoding1")]
    public function testEncoding1(string $encoding): void
    {
        self::assertSame($encoding, (new IsNoLongerThan(10, $encoding))->encoding());
    }

    public static function dataForTestMatches1(): iterable
    {
        foreach (DataFactory::positiveIntegers() as $length) {
            $length = DataFactory::unboxSingle($length);
            yield "string-no-longer-than-{$length}-string-equal-length" => [$length, str_repeat("m", $length)];
            yield "string-no-longer-than-{$length}-string-minus-one" => [$length, str_repeat("m", $length - 1)];
            yield "string-no-longer-than-{$length}-string-0" => [$length, ""];

            if (1 < $length) {
                yield "string-no-longer-than-{$length}-multibyte-utf8-characters" => [$length, str_repeat("\xc3\xa9", $length - 1)];
            }
        }
    }

    /** Ensure a reasonable subset of shorter strings match successfully. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(int $length, string $string): void
    {
        self::assertTrue((new IsNoLongerThan($length))->matches($string));
    }

    public static function dataForTestMatches2(): iterable
    {
        foreach (DataFactory::positiveIntegers() as $length) {
            $length = DataFactory::unboxSingle($length);
            yield "string-no-longer-than-{$length}-string-plus-one" => [$length, str_repeat("k", $length + 1)];
            yield "string-no-longer-than-{$length}-string-plus-more" => [$length, str_repeat("d", $length + 10)];
        }
    }

    /** Ensure a reasonable subset of longer strings fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(int $length, string $string): void
    {
        self::assertFalse((new IsNoLongerThan($length))->matches($string));
    }

    public static function dataForTestMatches3(): iterable
    {
        // U+00e9 (é)
        yield "string-no-longer-than-6-more-bytes-fewer-characters-u+00e9-utf8" => [6, "UTF-8", str_repeat("\xc3\xa9", 6)];
        yield "string-no-longer-than-6-more-bytes-fewer-characters-u+00e9-utf16" => [6, "UTF-16", str_repeat("\x00\xe9", 6)];
        yield "string-no-longer-than-6-more-bytes-fewer-characters-u+00e9-utf16le" => [6, "UTF-16LE", str_repeat("\xe9\x00", 6)];
        yield "string-no-longer-than-6-more-bytes-fewer-characters-u+00e9-utf16be" => [6, "UTF-16BE", str_repeat("\x00\xe9", 6)];
        yield "string-no-longer-than-6-more-bytes-fewer-characters-u+00e9-utf32le" => [6, "UTF-32LE", str_repeat("\x00\x00\xe9\x00", 6)];
        yield "string-no-longer-than-6-more-bytes-fewer-characters-u+00e9-utf32be" => [6, "UTF-32BE", str_repeat("\x00\x00\x00\xe9", 6)];

        // U+10437 (𐐷)
        yield "string-no-longer-than-3-more-bytes-fewer-characters-u+10437-utf8" => [3, "UTF-8", str_repeat("\xf0\x90\x90\xb7", 3)];
        yield "string-no-longer-than-3-more-bytes-fewer-characters-u+10437-utf16" => [3, "UTF-16", str_repeat("\xd8\x01\xdc\x37", 3)];
        yield "string-no-longer-than-3-more-bytes-fewer-characters-u+10437-utf16le" => [3, "UTF-16LE", str_repeat("\x01\xd8\x37\xdc", 3)];
        yield "string-no-longer-than-3-more-bytes-fewer-characters-u+10437-utf16be" => [3, "UTF-16BE", str_repeat("\xd8\x01\xdc\x37", 3)];
        yield "string-no-longer-than-3-more-bytes-fewer-characters-u+10437-utf31le" => [3, "UTF-32LE", str_repeat("\x01\x00\x37\x04", 3)];
        yield "string-no-longer-than-3-more-bytes-fewer-characters-u+10437-utf31be" => [3, "UTF-32BE", str_repeat("\x00\x01\x04\x37", 3)];
    }

    /** Ensure strings of more than length bytes but length characters in the required encoding match. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(int $length, string $encoding, string $encodedString): void
    {
        self::assertGreaterThanOrEqual($length, strlen($encodedString));
        self::assertTrue((new IsNoLongerThan($length, $encoding))->matches($encodedString));
    }

    public static function dataForTestMatches4(): iterable
    {
        foreach (self::SingleByteEncodings as $label => $encoding) {
            if (!array_key_exists($label, self::SingleByteEncodingStrings)) {
                continue;
            }

            $encoding = DataFactory::unboxSingle($encoding);
            $value = DataFactory::unboxSingle(self::SingleByteEncodingStrings[$label]);
            yield "string-no-longer-than-9-same-{$label}" => [9, $encoding, $value, true];
            yield "string-no-longer-than-8-longer-{$label}" => [8, $encoding, $value, false];
            yield "string-no-longer-than-10-shorter-{$label}" => [10, $encoding, $value, true];
        }
    }

    /** Ensure single-byte encodings (fail to) match using byte length. */
    #[DataProvider("dataForTestMatches4")]
    public function testMatches4(int $length, string $encoding, string $encodedString, bool $expected): void
    {
        self::assertSame($expected, (new IsNoLongerThan($length, $encoding))->matches($encodedString));
    }

    public static function dataForTestMatches5(): iterable
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
    #[DataProvider("dataForTestMatches5")]
    public function testMatches5(int $length, mixed $string): void
    {
        self::assertFalse((new IsNoLongerThan($length))->matches($string));
    }

    public static function dataForTestDescribe1(): iterable
    {
        yield from DataFactory::relabel(DataFactory::matrix(self::Lengths, [...self::SingleByteEncodings, ...self::MultiByteEncodings]), "string-no-longer-than-", RelabelMode::Prefix);
    }

    /** Ensure the matcher describes itself correctly according to the length and encoding. */
    #[DataProvider("dataForTestDescribe1")]
    public static function testDescribe1(int $length, string $encoding): void
    {
        self::assertSame("({$encoding}-string[<={$length}])", (new IsNoLongerThan($length, $encoding))->describe(self::nullSerialiser()));
    }

    /** Ensure the matcher describes itself with UTF-8 encoding by default. */
    public static function testDescribe2(): void
    {
        self::assertSame("(UTF-8-string[<=10])", (new IsNoLongerThan(10))->describe(self::nullSerialiser()));
    }
}
