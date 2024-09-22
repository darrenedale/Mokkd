<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Strings;

use LogicException;
use Mokkd\Matchers\Strings\IsStringOfFewerBytesThan;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsStringOfFewerBytesThanTest extends TestCase
{
    use CreatesNullSerialiser;

    public static function dataForTestConstructor1(): iterable
    {
        yield from DataFactory::integerZero();
        yield from DataFactory::negativeIntegers();
        yield from DataFactory::minInteger();
    }

    /** Ensure the constructor throws the expected exception with invalid lengths. */
    #[DataProvider("dataForTestConstructor1")]
    public function testConstructor1(int $length): void
    {
        self::skipIfAssertionsDisabled();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Expecting length > 0, found {$length}");
        new IsStringOfFewerBytesThan($length);
    }

    public static function dataForTestLength1(): iterable
    {
        yield from DataFactory::positiveIntegers();
        yield from DataFactory::maxInteger();
    }

    /** Ensure the constructor sets the length and we can retrieve it. */
    #[DataProvider("dataForTestLength1")]
    public function testLength1(int $length): void
    {
        self::assertSame($length, (new IsStringOfFewerBytesThan($length))->length());
    }

    public static function dataForTestMatches1(): iterable
    {
        foreach (DataFactory::positiveIntegers() as $length) {
            $length = DataFactory::unboxSingle($length);
            yield "string-just-shorter-than-{$length}-bytes" => [$length, str_repeat(chr(0x40 + ($length % 79)), $length - 1)];
            yield "empty-string-shorter-than-{$length}-bytes" => [$length, ""];
        }

        yield "string-shorter-than-int-max-bytes" => [PHP_INT_MAX, str_repeat("z", 16384)];
        yield "empty-string-shorter-than-int-max-bytes" => [PHP_INT_MAX, ""];
    }

    /** Ensure a reasonable subset of shorter strings match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(int $length, string $string): void
    {
        self::assertTrue((new IsStringOfFewerBytesThan($length))->matches($string));
    }

    public static function dataForTestMatches2(): iterable
    {
        foreach (DataFactory::positiveIntegers() as $length) {
            $length = DataFactory::unboxSingle($length);
            yield "string-just-longer-than-{$length}-bytes" => [$length, str_repeat(chr(0x40 + ($length % 79)), $length + 1)];
            yield "string-much-longer-than-{$length}-bytes" => [$length, str_repeat(chr(0x40 + ($length % 79)), $length + 100)];
            yield "string-leading-whitespace-longer-than-{$length}-bytes" => [$length, " " . str_repeat(chr(0x40 + ($length % 79)), $length)];
            yield "string-trailing-whitespace-longer-than-{$length}-bytes" => [$length, str_repeat(chr(0x40 + ($length % 79)), $length) . " "];
            yield "string-whitespace-longer-than-{$length}-bytes" => [$length, str_repeat(" ", $length + 1)];
            yield "string-null-byte-longer-than-{$length}-bytes" => [$length, str_repeat(chr(0x40 + ($length % 79)), $length) . "\0"];

            if (0 < $length) {
                yield "string-surrounding-whitespace-longer-than-{$length}-bytes" => [$length, " " . str_repeat(chr(0x40 + ($length % 79)), $length - 1) . " "];
            }
        }
    }

    /** Ensure a reasonable subset of longer strings fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(int $length, string $string): void
    {
        self::assertFalse((new IsStringOfFewerBytesThan($length))->matches($string));
    }

    public static function dataForTestMatches3(): iterable
    {
        foreach (DataFactory::positiveIntegers() as $length) {
            $length = DataFactory::unboxSingle($length);
            yield "string-of-exactly-{$length}-bytes" => [$length, str_repeat(chr(0x40 + ($length % 79)), $length)];
            yield "string-null-byte-of-exactly-{$length}-bytes" => [$length, str_repeat(chr(0x40 + ($length % 79)), $length - 1) . "\0"];
            yield "string-leading-whitespace-of-exactly-{$length}-bytes" => [$length, " " . str_repeat(chr(0x40 + ($length % 79)), $length - 1)];
            yield "string-trailing-whitespace-of-exactly-{$length}-bytes" => [$length, str_repeat(chr(0x40 + ($length % 79)), $length - 1) . " "];
            yield "string-whitespace-of-exactly-{$length}-bytes" => [$length, str_repeat(" ", $length)];

            if (1 < $length) {
                yield "string-surrounding-whitespace-of-exactly-{$length}-bytes" => [$length, " " . str_repeat(chr(0x40 + ($length % 79)), $length - 2) . " "];
            }
        }
    }

    /** Ensure a reasonable subset of strings of exactly the length fail to match. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(int $length, string $string): void
    {
        self::assertFalse((new IsStringOfFewerBytesThan($length))->matches($string));
    }

    public static function dataForTestDescribe1(): iterable
    {
        yield from DataFactory::positiveIntegers();
    }

    /** Ensure the matcher describes itself as expected. */
    #[DataProvider("dataForTestDescribe1")]
    public static function testDescribe1(int $length): void
    {
        self::assertSame("(string[<{$length}])", (new IsStringOfFewerBytesThan($length))->describe(self::nullSerialiser()));
    }
}
