<?php

declare(strict_types=1);

namespace MokkdTests\Utilities;

use Mokkd\Utilities\Serialiser;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use Stringable;

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

    public static function dataForTestSerialiseFloat1(): iterable
    {
        yield "zero" => [0.0, "(float) 0.0"];
        yield "negative" => [-3.14, "(float) -3.14"];
        yield "positive" => [3.14, "(float) 3.14"];
        yield "integer" => [3.0, "(float) 3.0"];
        yield "rounds-correctly" => [3.14159265359, "(float) 3.141592654"];
        yield "trims-decimal-places" => [3.141592700000, "(float) 3.1415927"];
    }

    /** Ensure floats are serialised as expected. */
    #[DataProvider("dataForTestSerialiseFloat1")]
    public function testSerialiseFloat1(float $value, string $expected): void
    {
        $serialiseFloat = self::accessibleMethod($this->serialiser, "serialiseFloat");
        self::assertSame($expected, $serialiseFloat($value));
    }

    public static function dataForTestSerialiseBool1(): iterable
    {
        yield "false" => [false, "false"];
        yield "true" => [true, "true"];
    }

    /** Ensure bools are serialised as expected. */
    #[DataProvider("dataForTestSerialiseBool1")]
    public function testSerialiseBool1(bool $value, string $expected): void
    {
        $serialiseBool = self::accessibleMethod($this->serialiser, "serialiseBool");
        self::assertSame($expected, $serialiseBool($value));
    }

    public static function dataForTestSerialiseArray1(): iterable
    {
        yield "empty" => [[], "[]"];
        yield "strings" => [["one", "three", "two"], "[(string[3]) \"one\", (string[5]) \"three\", (string[3]) \"two\"]"];
        yield "ints" => [[1, 3, 2], "[(int) 1, (int) 3, (int) 2]"];
        yield "floats" => [[1.1, 3.14159265359, 2.0], "[(float) 1.1, (float) 3.141592654, (float) 2.0]"];
        yield "mmixed" => [[1.1, "3.14159265359", false], "[(float) 1.1, (string[13]) \"3.14159265359\", false]"];
        yield "excess-elements" => [[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], "[(int) 1, (int) 2, (int) 3, (int) 4, (int) 5, (int) 6, (int) 7, (int) 8, (int) 9, (int) 10, …]"];
    }

    /** Ensure arrays are serialised as expected. */
    #[DataProvider("dataForTestSerialiseArray1")]
    public function testSerialiseArray1(array $value, string $expected): void
    {
        $serialiseArray = self::accessibleMethod($this->serialiser, "serialiseArray");
        self::assertSame($expected, $serialiseArray($value));
    }

    public static function dataForTestSerialiseObject1(): iterable
    {
        yield "stdClass" => [new stdClass(), "\\(stdClass\\)", ];
        yield "named" => [new Serialiser(), "\\(Mokkd\\\\Utilities\\\\Serialiser\\)"];
        yield "anonymous" => [new class{}, "\\(class@anonymous.*\\)"];
        yield "named-stringable" => [new NamedStringable(), "\\(MokkdTests\\\\Utilities\\\\NamedStringable\\) NamedStringable test class"];

        yield "anonymous-stringable" => [
            new class implements Stringable
            {
                public function __toString(): string
                {
                    return "The test class instance";
                }
            },
            "\\(Stringable@anonymous.*\\) The test class instance",
        ];
    }

    /** TODO Ensure resources are serialised as expected. */

    /** Ensure objects are serialised as expected. */
    #[DataProvider("dataForTestSerialiseObject1")]
    public function testSerialiseObject1(object $object, string $expectedPattern): void
    {
        $serialiseObject = self::accessibleMethod($this->serialiser, "serialiseObject");
        self::assertMatchesRegularExpression("/^{$expectedPattern}\$/", $serialiseObject($object));
    }

    /** Ensure null is serialised as expected. */
    public function testSerialiseNull1(): void
    {
        $serialiseNull = self::accessibleMethod($this->serialiser, "serialiseNull");
        self::assertSame("NULL", $serialiseNull(null));
    }

    /** TODO Ensure serialise() delegates to the expected helper. */
}
