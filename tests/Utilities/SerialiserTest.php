<?php

declare(strict_types=1);

namespace MokkdTests\Utilities;

use Mokkd\Utilities\Serialiser;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class SerialiserTest extends TestCase
{
    private Serialiser $serialiser;

    public function setUp(): void
    {
        $this->serialiser = new Serialiser();
    }

    public function tearDown(): void
    {
        unset($this->serialiser);
    }

    public static function dataForTestElideString1(): iterable
    {
        yield "empty" => ["", ""];
        yield "whitespace" => ["  ", "  "];
        yield "no-elision" => ["A short string", "A short string"];
        yield "elision" => ["A longer string that needs elision", "A longer string that needs el…"];
        yield "almost-needs-elision" => ["A string of thirty characters.", "A string of thirty characters."];
        yield "just-needs-elision" => ["A string containing thirty-one.", "A string containing thirty-on…"];
    }

    /** Ensure strings are elided as expected. */
    #[DataProvider("dataForTestElideString1")]
    public function testElideString1(string $str, string $expected): void
    {
        $elideString = self::accessibleMethod($this->serialiser, "elideString");
        self::assertSame($expected, $elideString($str));
    }

    public static function dataForTestSerialiseString1(): iterable
    {
        yield "empty" => ["", "(string[0]) \"\""];
        yield "whitespace" => ["  ", "(string[2]) \"  \""];
        yield "no-elision" => ["A short string", "(string[14]) \"A short string\""];
        yield "elision" => ["A longer string that needs elision", "(string[34]) \"A longer string that needs el…\""];
        yield "almost-needs-elision" => ["A string of thirty characters.", "(string[30]) \"A string of thirty characters.\""];
        yield "just-needs-elision" => ["A string containing thirty-one.", "(string[31]) \"A string containing thirty-on…\""];
    }

    /** Ensure strings are serialised as expected. */
    #[DataProvider("dataForTestSerialiseString1")]
    public function testSerialiseString1(string $str, string $expected): void
    {
        $serialiseString = self::accessibleMethod($this->serialiser, "serialiseString");
        self::assertSame($expected, $serialiseString($str));
    }

    public static function dataForTestSerialiseInt1(): iterable
    {
        yield "zero" => [0, "(int) 0"];
        yield "negative" => [-1, "(int) -1"];
        yield "one" => [1, "(int) 1"];
        yield "forty-two" => [42, "(int) 42"];
        yield "int-min" => [PHP_INT_MIN, "(int) " . PHP_INT_MIN];
        yield "int-max" => [PHP_INT_MAX, "(int) " . PHP_INT_MAX];
    }

    /** Ensure ints are serialised as expected. */
    #[DataProvider("dataForTestSerialiseInt1")]
    public function testSerialiseInt1(int $value, string $expected): void
    {
        $serialiseInt = self::accessibleMethod($this->serialiser, "serialiseInt");
        self::assertSame($expected, $serialiseInt($value));
    }
}
