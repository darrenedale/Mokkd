<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Types;

use Mokkd\Matchers\Types\IsBool;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsBoolTest extends TestCase
{
    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::booleans();
    }

    /** Ensure all bools successfully match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(bool $test): void
    {
        self::assertTrue((new IsBool())->matches($test));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::strings();
        yield from DataFactory::integers();
        yield from DataFactory::floats();
        yield from DataFactory::arrays();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
    }

    /** Ensure non-bools fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $test): void
    {
        self::assertFalse((new IsBool())->matches($test));
    }

    /** Ensure the IsBool matcher describes itself as expected. */
    public function testDescribe1(): void
    {
        self::assertSame("(bool) {any}", (new IsBool())->describe(self::nullSerialiser()));
    }
}
