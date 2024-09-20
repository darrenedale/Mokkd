<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Types;

use Mokkd\Matchers\Types\IsOpenResource;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsOpenResourceTest extends TestCase
{
    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::openResources();
    }

    /** Ensure all open resources (or a reasonable approximation of the set of resources) successfully match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(mixed $test): void
    {
        self::assertTrue((new IsOpenResource())->matches($test));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::booleans();
        yield from DataFactory::integers();
        yield from DataFactory::floats();
        yield from DataFactory::strings();
        yield from DataFactory::arrays();
        yield from DataFactory::objects();
        yield from DataFactory::closedResources();
    }

    /** Ensure non-resources and closed resources fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $test): void
    {
        self::assertFalse((new IsOpenResource())->matches($test));
    }

    /** Ensure the IsOpenResource matcher describes itself as expected. */
    public function testDescribe1(): void
    {
        self::assertSame("(resource) {open}", (new IsOpenResource())->describe(self::nullSerialiser()));
    }
}
