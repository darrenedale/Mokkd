<?php

declare(strict_types=1);

namespace MokkdTests\Matchers;

use Mokkd\Matchers\Identity;
use Mokkd\Utilities\Serialiser;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(Identity::class)]
class IdentityTest extends TestCase
{
    public static function dataForTestMatches1(): iterable
    {
        yield "strings" => ["mokkd", "mokkd"];
        yield "ints" => [42, 42];
        yield "floats" => [3.1415927, 3.1415927];
        yield "null" => [null, null];
        yield "arrays" => [[1, 2, "mokkd", 3], [1, 2, "mokkd", 3]];
        yield "true" => [true, true];
        yield "false" => [false, false];
    }

    /** Ensure values that are identical match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(mixed $matchAgainst, mixed $value): void
    {
        self::assertTrue((new Identity($matchAgainst))->matches($value));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "int-string" => [42, "42"];
        yield "string-int" => ["42", 42];
        yield "int-float" => [42, 42.0];
        yield "float-int" => [42.0, 42];
        yield "string-float" => ["3.1415927", 3.1415927];
        yield "float-string" => [3.1415927, "3.1415927"];

        yield "null-empty-string" => [null, ""];
        yield "empty-string-null" => ["", null];
        yield "null-zero-int" => [null, 0];
        yield "zero-int-null" => [0, null];
        yield "null-zero-float" => [null, 0.0];
        yield "zero-float-null" => [0.0, null];
        yield "null-false" => [null, false];
        yield "false-null" => [false, null];

        yield "arrays" => [[1, 2, "mokkd", 3], ["1", 2, "mokkd", 3]];

        yield "false-zero-int" => [false, 0];
        yield "zero-int-false" => [0, false];
        yield "false-zero-float" => [false, 0.0];
        yield "zero-float-false" => [0.0, false];
        yield "false-empty-string" => [false, ""];
        yield "empty-string-false" => ["", false];
        yield "false-zero-int-string" => [false, "0"];
        yield "zero-int-string-false" => ["0", false];
        yield "false-zero-float-string" => [false, "0.0"];
        yield "zero-float-string-false" => ["0.0", false];

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

    /** Ensure values that are only equal don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $matchAgainst, mixed $value): void
    {
        self::assertFalse((new Identity($matchAgainst))->matches($value));
    }

    public static function dataForTestMatches3(): iterable
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

        yield "true-int" => [true, 0];
        yield "int-true" => [0, true];
        yield "true-float" => [true, 0.0];
        yield "float-true" => [0.0, true];
        yield "true-string" => [true, "mokkd"];
        yield "string-true" => ["mokkd", true];
    }

    /** Ensure values that are not even equal don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches3(mixed $matchAgainst, mixed $value): void
    {
        self::assertFalse((new Identity($matchAgainst))->matches($value));
    }

    /** Ensure objects must be the same instance. */
    public function testMatches4(): void
    {
        $object = new class{};
        self::assertTrue((new Identity($object))->matches($object));
        self::assertFalse((new Identity($object))->matches(clone $object));
    }

    public static function dataForTestToString1(): iterable
    {
        $serialiser = new Serialiser();

        foreach (self::dataForTestMatches1() as $key => $matchAgainst) {
            yield $key => [$matchAgainst, $serialiser->serialise($matchAgainst)];
        }

        $fh = fopen("php://memory", "r");
        yield "resource" => [$fh, $serialiser->serialise($fh)];

        $fh = fopen("php://memory", "r");
        fclose($fh);
        yield "closed-resource" => [$fh, $serialiser->serialise($fh)];

        $object = new class{};
        yield "anonymous-object" => [$object, $serialiser->serialise($object)];

        yield "object" => [$serialiser, $serialiser->serialise($serialiser)];
    }

    /** Ensure the serialiser is used to produce string representations of the value being matched against. */
    #[DataProvider("dataForTestToString1")]
    public function testToString1(mixed $value, string $expected): void
    {
        self::assertSame($expected, (string) (new Identity($value)));
    }
}
