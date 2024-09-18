<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Types;

use Mokkd\Matchers\Types\IsArray;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsListTest extends TestCase
{
    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::arrays();
    }

    /** Ensure all lists (or a sensible approximation thereof) successfully match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(array $test): void
    {
        self::assertTrue((new IsArray())->matches($test));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::strings();
        yield from DataFactory::integers();
        yield from DataFactory::floats();
        yield from DataFactory::booleans();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
    }

    /** Ensure non-arrays fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $test): void
    {
        self::assertFalse((new IsArray())->matches($test));
    }

    /** Ensure the IsArray matcher describes itself as expected. */
    public function testDescribe1(): void
    {
        self::assertSame("(array) {any}", (new IsArray())->describe(self::nullSerialiser()));
    }
}
