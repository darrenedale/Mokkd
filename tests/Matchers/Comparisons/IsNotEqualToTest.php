<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Comparisons;

use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Comparisons\IsNotEqualTo;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(IsNotEqualTo::class)]
class IsNotEqualToTest extends TestCase
{
    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::identicalValues();
        yield from DataFactory::equalValues();
    }

    /** Ensure values that are equal don't match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(mixed $matchAgainst, mixed $value): void
    {
        self::assertFalse((new IsNotEqualTo($matchAgainst))->matches($value));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield from DataFactory::unequalValues();
    }

    /** Ensure values that are not equal don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $matchAgainst, mixed $value): void
    {
        self::assertTrue((new IsNotEqualTo($matchAgainst))->matches($value));
    }

    /** Ensure identical objects don't match. */
    public function testMatches3(): void
    {
        $object = new class {};
        self::assertFalse((new IsNotEqualTo($object))->matches($object));
    }

    /** Ensure equal objects don't match. */
    public function testMatches4(): void
    {
        $object = new class {};
        self::assertfalse((new IsNotEqualTo($object))->matches(clone $object));
    }

    /** Ensure resources match like values. */
    public function testMatches5(): void
    {
        $resource = fopen("php://memory", "r");
        $equalResource = $resource;
        self::assertFalse((new IsNotEqualTo($resource))->matches($equalResource));
    }

    /** Ensure the serialiser is used to describe the matcher. */
    public function testDescribe1(): void
    {
        $serialiser = new class implements SerialiserContract
        {
            public function serialise(mixed $value): string
            {
                IsNotEqualToTest::assertSame("test value", $value);
                return "(test-string) \"test value\"";
            }
        };

        self::assertSame("!= (test-string) \"test value\"", (new IsNotEqualTo("test value"))->describe($serialiser));
    }
}
