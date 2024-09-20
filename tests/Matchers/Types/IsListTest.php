<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Types;

use Mokkd\Matchers\Types\IsList;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsListTest extends TestCase
{
    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::listArrays();
    }

    /** Ensure all lists (or a reasonable approximation thereof) successfully match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(array $test): void
    {
        self::assertTrue((new IsList())->matches($test));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::nonEmptyAssociativeArrays();
        yield from DataFactory::nonEmptyPropertyMaps();
        yield from DataFactory::strings();
        yield from DataFactory::integers();
        yield from DataFactory::floats();
        yield from DataFactory::booleans();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
    }

    /** Ensure non-lists fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $test): void
    {
        self::assertFalse((new IsList())->matches($test));
    }

    /** Ensure the IsList matcher describes itself as expected. */
    public function testDescribe1(): void
    {
        self::assertSame("(array) {list}", (new IsList())->describe(self::nullSerialiser()));
    }
}
