<?php

declare(strict_types=1);

namespace MokkdTests\Matchers;

use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Comparisons\IsEqualTo;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(IsEqualTo::class)]
class EqualityTest extends TestCase
{
    public static function dataForTestMatches1(): iterable
    {
        yield "strings" => ["mokkd", "mokkd"];
        yield "string-int" => ["42", 42];
        yield "string-float" => ["3.1415927", 3.1415927];

        yield "identical-arrays" => [[1, 2, "mokkd", 3], [1, 2, "mokkd", 3]];
        yield "equal-arrays" => [[1, 2, "mokkd", 3], ["1", 2, "mokkd", 3]];

        yield "ints" => [42, 42];
        yield "int-string" => [42, "42"];
        yield "int-float" => [42, 42.0];

        yield "floats" => [3.1415927, 3.1415927];
        yield "float-int" => [42.0, 42];
        yield "float-string" => [3.1415927, "3.1415927"];

        yield "null" => [null, null];
        yield "null-empty-string" => [null, ""];
        yield "empty-string-null" => ["", null];
        yield "null-zero-int" => [null, 0];
        yield "zero-int-null" => [0, null];
        yield "null-zero-float" => [null, 0.0];
        yield "zero-float-null" => [0.0, null];

        yield "false" => [false, false];
        yield "false-zero-int" => [false, 0];
        yield "zero-int-false" => [0, false];
        yield "false-zero-float" => [false, 0.0];
        yield "zero-float-false" => [0.0, false];
        yield "false-empty-string" => [false, ""];
        yield "empty-string-false" => ["", false];
        yield "false-zero-int-string" => [false, "0"];
        yield "zero-int-string-false" => ["0", false];

        yield "true" => [true, true];
        yield "true-non-zero-int" => [true, 1];
        yield "non-zero-int-true" => [1, true];
        yield "true-non-zero-float" => [true, 1.1];
        yield "non-zero-float-true" => [1.1, true];
        yield "true-non-empty-string" => [true, "x"];
        yield "non-empty-string-true" => ["x", true];
        yield "true-non-zero-int-string" => [true, "1"];
        yield "non-zero-int-string-true" => ["1", true];
        yield "true-non-zero-float-string" => [true, "1.1"];
        yield "non-zero-float-string-true" => ["1.1", true];
    }

    /** Ensure values that are equal match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(mixed $matchAgainst, mixed $value): void
    {
        self::assertTrue((new IsEqualTo($matchAgainst))->matches($value));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "int-string" => [42, "forty-two"];
        yield "string-int" => ["forty-two", 42];
        yield "int-float" => [42, 42.1];
        yield "float-int" => [42.1, 42];
        yield "int-int" => [42, 41];

        yield "float-float" => [42.1, 42.0];
        yield "string-float" => ["3.1415926", 3.1415927];
        yield "float-string" => [3.1415926, "3.1415927"];

        yield "null-string" => [null, "mokkd"];
        yield "string-null" => ["mokkd", null];
        yield "null-int" => [null, 1];
        yield "int-null" => [-1, null];
        yield "null-float" => [null, 0.1];
        yield "float-null" => [-0.1, null];

        yield "arrays" => [[1, 2, "mokkd", 3], [2, 1, "mokkd", 3]];

        yield "false-int" => [false, 1];
        yield "int-false" => [1, false];
        yield "false-float" => [false, 0.1];
        yield "zero-float" => [-0.1, false];
        yield "false-string" => [false, "mokkd"];
        yield "string-false" => ["mokkd", false];

        // if PHP were consistent, these would be equal (just as "0" equals false); but it's not ...
        yield "false-zero-float-string" => [false, "0.0"];
        yield "zero-float-string-false" => ["0.0", false];

        yield "true-int" => [true, 0];
        yield "int-true" => [0, true];
        yield "true-float" => [true, 0.0];
        yield "float-true" => [0.0, true];
        yield "true-string" => [true, ""];
        yield "string-true" => ["", true];
    }

    /** Ensure values that are not equal don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $matchAgainst, mixed $value): void
    {
        self::assertFalse((new IsEqualTo($matchAgainst))->matches($value));
    }

    /** Ensure identical objects match. */
    public function testMatches3(): void
    {
        $object = new class{};
        self::assertTrue((new IsEqualTo($object))->matches($object));
    }

    /** Ensure equal objects match. */
    public function testMatches4(): void
    {
        $object = new class{};
        self::assertTrue((new IsEqualTo($object))->matches(clone $object));
    }

    /** Ensure the serialiser is used to describe the Equality matcher. */
    public function testDescribe1(): void
    {
        $serialiser = new class implements SerialiserContract
        {
            public function serialise(mixed $value): string
            {
                EqualityTest::assertSame("test value", $value);
                return "The test-serialised value";
            }
        };

        self::assertSame("The test-serialised value", (new IsEqualTo("test value"))->describe($serialiser));
    }
}
