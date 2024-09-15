<?php

declare(strict_types=1);

namespace MokkdTests\Mappers;

use LogicException;
use Mokkd\Mappers\IndexedArgument;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;

#[CoversClass(IndexedArgument::class)]
class IndexedArgumentTest extends TestCase
{
    /** Ensure constructor requires an int index. */
    public function testConstructor1(): void
    {
        $constructor = (new ReflectionClass(IndexedArgument::class))->getConstructor();
        self::assertInstanceOf(ReflectionMethod::class, $constructor);
        $parameters = $constructor->getParameters();
        self::assertCount(1, $parameters);
        self::assertTrue($parameters[0]->hasType());
        self::assertSame("int", $parameters[0]->getType()->getName());
    }

    public static function dataForTestConstructor2(): iterable
    {
        yield "negative" => [-1];
        yield "min-int" => [PHP_INT_MIN];
    }

    /** Ensure constructor throws with invalid argument. */
    #[DataProvider("dataForTestConstructor2")]
    public function testConstructor2(int $index): void
    {
        self::skipIfAssertionsDisabled();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Expected index >= 0, found {$index}");
        new IndexedArgument($index);
    }

    public static function dataForTestIndex1(): iterable
    {
        yield "zero" =>[0];
        yield "max-int" =>[PHP_INT_MAX];
    }

    /** Ensure constructor throws with invalid argument. */
    #[DataProvider("dataForTestIndex1")]
    public function testIndex1(int $index): void
    {
        self::assertSame($index, (new IndexedArgument($index))->index());
    }


    public static function dataForTestMapKey1(): iterable
    {
        yield "zero-empty" => [0];
        yield "one-empty" => [1];
        yield "int-max-empty" => [PHP_INT_MAX];
        yield "one-one" => [1, "key-1"];
        yield "two-two" => [2, "key-1", "key-2"];
        yield "three-two" => [3, "key-1", "key-2"];
        yield "int-max-ten" => [PHP_INT_MAX, "key-1", "key-2", "key-3", "key-4", "key-5", "key-6", "key-7", "key-8", "key-9", "key-10"];
    }

    /** Ensure mapping throws with insufficient arguments. */
    #[DataProvider("dataForTestMapKey1")]
    public function testMapKey1(int $index, mixed ...$args): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Not enough arguments to select argument #{$index} as the mapped return value key");
        (new IndexedArgument($index))->mapKey(...$args);
    }

    public static function dataForTestMapKey2(): iterable
    {
        yield "null" => [1, "key", null, 1];
        yield "negative" => [2, 3, "key", -1];
        yield "min-int" => [1, 2, PHP_INT_MIN, "key"];
        yield "float" => [0, 3.1415926, "key", 1];
        yield "true" => [2, 2, 1, true];
        yield "false" => [1, 2, false, "key"];
        yield "object" => [2, 2, "key", new class {}];
        yield "array" => [1, "key", [], 0];
        yield "resource" => [0, fopen("php://memory", "r"), "key", 1];
    }

    /** Ensure mapping throws with unsuitable arg. */
    #[DataProvider("dataForTestMapKey2")]
    public function testMapKey2(int $index, mixed ...$args): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Argument #{$index} is not a valid mapped return value key");
        (new IndexedArgument($index))->mapKey(...$args);
    }

    public static function dataForTestMapKey3(): iterable
    {
        yield "first" => [0, "key", "key", null, 1];
        yield "last" => [2, 0, 3, "key", 0];
        yield "first-of-one" => [0, "test-key", "test-key"];
    }

    /** Ensure mapping provides the expected key. */
    #[DataProvider("dataForTestMapKey3")]
    public function testMapKey3(int $index, int|string $expectedKey, mixed ...$args): void
    {
        self::assertSame($expectedKey, (new IndexedArgument($index))->mapKey(...$args));
    }
}
