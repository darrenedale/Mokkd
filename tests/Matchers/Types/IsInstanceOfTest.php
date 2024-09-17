<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Types;

use Mokkd\Matchers\Types\IsInstanceOf;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

class IsInstanceOfTest extends TestCase
{
    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        foreach (DataFactory::objects() as $label => $object) {
            $object = DataFactory::unboxSingle($object);
            $className = $object::class;
            yield $label => [$className, $object];
            yield "{$label}-leading-separator" => ["\\{$className}", $object];
        }
    }

    /** Ensure instances of the correct class successfully match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(string $className, object $instance): void
    {
        self::assertTrue((new IsInstanceOf($className))->matches($instance));
    }

    public static function dataForTestMatches2(): iterable
    {
        foreach (DataFactory::objects() as $label => $object) {
            $object = DataFactory::unboxSingle($object);
            $className = $object::class;
            yield "{$label}-leading-whitespace" => [" {$className}", $object];
            yield "{$label}-trailing-whitespace" => ["{$className} ", $object];
            yield "{$label}-surrounding-whitespace" => [" {$className} ", $object];
            yield "{$label}-extra-trailing-separator" => ["{$className}\\", $object];
            yield "{$label}-extra-surrounding-separators" => ["\\{$className}\\", $object];
            
            if (str_contains($className, "\\")) {
                yield "{$label}-duplicate-separators" => [str_replace("\\", "\\\\", $className), $object];
            }
        }
    }

    /** Ensure class names with common irregularities don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(string $className, object $instance): void
    {
        self::assertFalse((new IsInstanceOf($className))->matches($instance));
    }

    public static function dataForTestMatches3(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::booleans();
        yield from DataFactory::integers();
        yield from DataFactory::floats();
        yield from DataFactory::strings();
        yield from DataFactory::arrays();
        yield from DataFactory::closedResources();
        yield from DataFactory::openResources();
    }

    /** Ensure non-objects fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches3(mixed $test): void
    {
        self::assertFalse((new IsInstanceOf(stdClass::class))->matches($test));
    }

    public static function dataForTestDescribe1(): iterable
    {
        yield from DataFactory::classNames();
    }

    /** Ensure the IsInstanceOf matcher describes itself as expected. */
    #[DataProvider("dataForTestDescribe1")]
    public function testDescribe1(string $className): void
    {
        self::assertSame("({$className}) {any}", (new IsInstanceOf($className))->describe(self::nullSerialiser()));
    }
}
