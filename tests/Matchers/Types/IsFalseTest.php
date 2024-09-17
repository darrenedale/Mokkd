<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Types;

use Mokkd\Matchers\Types\IsFalse;
use MokkdTests\CreatesMockSerialiser;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsFalseTest extends TestCase
{
    use CreatesMockSerialiser;

    /** Ensure false successfully matches. */
    public function testMatches1(): void
    {
        self::assertTrue((new IsFalse())->matches(false));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::strings();
        yield from DataFactory::integers();
        yield from DataFactory::floats();
        yield from DataFactory::booleanTrue();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
    }

    /** Ensure values other than false fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $test): void
    {
        self::assertFalse((new IsFalse())->matches($test));
    }

    /** Ensure the IsFalse matcher describes itself as expected. */
    public function testDescribe1(): void
    {
        self::assertSame("(bool) false", (new IsFalse())->describe(self::mockSerialiser(false, "(bool) false")));
    }
}
