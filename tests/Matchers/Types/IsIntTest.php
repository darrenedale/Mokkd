<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Types;

use Mokkd\Matchers\Types\IsInt;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsIntTest extends TestCase
{
    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::integers();
    }

    /** Ensure all ints (or a reasonable approximation of the set of ints) successfully match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(int $test): void
    {
        self::assertTrue((new IsInt())->matches($test));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::strings();
        yield from DataFactory::booleans();
        yield from DataFactory::floats();
        yield from DataFactory::arrays();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
    }

    /** Ensure non-ints fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $test): void
    {
        self::assertFalse((new IsInt())->matches($test));
    }

    /** Ensure the IsInt matcher describes itself as expected. */
    public function testDescribe1(): void
    {
        self::assertSame("(int) {any}", (new IsInt())->describe(self::nullSerialiser()));
    }
}
