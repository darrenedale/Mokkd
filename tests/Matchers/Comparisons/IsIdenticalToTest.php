<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Comparisons;

use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Comparisons\IsIdenticalTo;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(IsIdenticalTo::class)]
class IsIdenticalToTest extends TestCase
{
    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::identicalValues();
    }

    /** Ensure values that are identical match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(mixed $matchAgainst, mixed $value): void
    {
        self::assertTrue((new IsIdenticalTo($matchAgainst))->matches($value));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield from DataFactory::equalValues();
        yield from DataFactory::unequalValues();
    }

    /** Ensure values that aren't identical don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $matchAgainst, mixed $value): void
    {
        self::assertFalse((new IsIdenticalTo($matchAgainst))->matches($value));
    }

    /** Ensure objects must be the same instance to match. */
    public function testMatches4(): void
    {
        $object = new class {};
        self::assertTrue((new IsIdenticalTo($object))->matches($object));
        self::assertFalse((new IsIdenticalTo($object))->matches(clone $object));
    }

    /** Ensure resources match like values. */
    public function testMatches5(): void
    {
        $resource = fopen("php://memory", "r");
        $identicalResource = $resource;
        self::assertTrue((new IsIdenticalTo($resource))->matches($resource));
        self::assertTrue((new IsIdenticalTo($resource))->matches($identicalResource));
    }

    /** Ensure the serialiser is used to describe the matcher. */
    public function testDescribe1(): void
    {
        $serialiser = new class implements SerialiserContract
        {
            public function serialise(mixed $value): string
            {
                IsEqualToTest::assertSame("test value", $value);
                return "The test-serialised value";
            }
        };

        self::assertSame("The test-serialised value", (new IsIdenticalTo("test value"))->describe($serialiser));
    }
}
