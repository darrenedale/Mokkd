<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Types;

use Mokkd\Matchers\Types\IsString;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsStringTest extends TestCase
{
    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::strings();
    }

    /** Ensure all strings (or a sensible approximation thereof) successfully match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(string $test): void
    {
        self::assertTrue((new IsString())->matches($test));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::arrays();
        yield from DataFactory::integers();
        yield from DataFactory::floats();
        yield from DataFactory::booleans();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
    }

    /** Ensure non-strings fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $test): void
    {
        self::assertFalse((new IsString())->matches($test));
    }

    /** Ensure the IsString matcher describes itself as expected. */
    public function testDescribe1(): void
    {
        self::assertSame("(string) {any}", (new IsString())->describe(self::nullSerialiser()));
    }
}
