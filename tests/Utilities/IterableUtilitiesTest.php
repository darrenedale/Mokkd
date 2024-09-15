<?php

declare(strict_types=1);

namespace MokkdTests\Utilities;

use ArrayIterator;
use Generator;
use Mokkd\Utilities\IterableAlgorithms;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Traversable;

class IterableUtilitiesTest extends TestCase
{
    private static function createGenerator(array $items): Generator
    {
        yield from $items;
    }

    private static function createTraversable(array $items): Traversable
    {
        return new ArrayIterator($items);
    }

    public static function emptyIterables(): iterable
    {
        $data = [];
        yield "int-array" => [$data];
        yield "int-generator" => [self::createGenerator($data)];
        yield "int-traversable" => [self::createTraversable($data)];
    }

    public static function intIterables(): iterable
    {
        $data = [1, 2, 3];
        yield "int-array" => [$data];
        yield "int-generator" => [self::createGenerator($data)];
        yield "int-traversable" => [self::createTraversable($data)];
    }

    public static function stringIterables(): iterable
    {
        $data = ["mokkd", "lib", "func"];
        yield "string-array" => [$data];
        yield "string-generator" => [self::createGenerator($data)];
        yield "string-traversable" => [self::createTraversable($data)];
    }

    public static function mixedIterables(): iterable
    {
        $data = ["mokkd", 42, 3.1415926];
        yield "string-int-float-array" => [$data];
        yield "string-int-float-generator" => [self::createGenerator($data)];
        yield "string-int-float-traversable" => [self::createTraversable($data)];
    }

    public static function keyedIterables(): iterable
    {
        $data = ["mokkd" => "func", "meaning" => 42, 3 => 3.1415926];
        yield "string-string-int-keys-array" => [$data];
        yield "string-string-int-keys-generator" => [self::createGenerator($data)];
        yield "string-string-int-keys-tarversable" => [self::createTraversable($data)];
    }

    public static function iterables(): iterable
    {
        yield from self::emptyIterables();
        yield from self::intIterables();
        yield from self::stringIterables();
        yield from self::mixedIterables();
        yield from self::keyedIterables();
    }

    public static function nonEmptyIterables(): iterable
    {
        yield from self::intIterables();
        yield from self::stringIterables();
        yield from self::mixedIterables();
    }

    public static function dataForTestAll1(): iterable
    {
        yield from self::nonEmptyIterables();
    }

    /** Ensure all sorts of iterables pass with a closure. */
    #[DataProvider("dataForTestAll1")]
    public function testAll1(iterable $testData): void
    {
        $called = false;

        $predicate = static function(mixed $value) use (&$called): bool {
            $called = true;
            return true;
        };

        self::assertSame(true, IterableAlgorithms::all($testData, $predicate));
        self::assertTrue($called);
    }

    public static function dataForTestAll2(): iterable
    {
        yield from self::nonEmptyIterables();
    }

    /** Ensure all sorts of iterables fail with a closure, and it's called only once. */
    #[DataProvider("dataForTestAll2")]
    public function testAll2(iterable $testData): void
    {
        $called = false;

        $predicate = static function(mixed $value) use (&$called): bool {
            IterableUtilitiesTest::assertFalse($called);
            $called = true;
            return false;
        };

        self::assertSame(false, IterableAlgorithms::all($testData, $predicate));
        self::assertTrue($called);
    }

    public static function dataForTestAll3(): iterable
    {
        yield from self::emptyIterables();
    }

    /** Ensure empty iterables pass without calling predicate. */
    #[DataProvider("dataForTestAll3")]
    public function testAll3(iterable $testData): void
    {
        $called = false;

        $predicate = static function(mixed $value) use (&$called): bool {
            IterableUtilitiesTest::fail("The predicate should not be called");
        };

        self::assertSame(true, IterableAlgorithms::all($testData, $predicate));
        self::assertFalse($called);
    }

    public static function dataForTestAll4(): iterable
    {
        yield from self::mixedIterables();
    }

    /** Ensure algorithm exits on first failed predicate call. */
    #[DataProvider("dataForTestAll4")]
    public function testAll4(iterable $testData): void
    {
        $called = 0;

        // data is string,int,float
        $predicate = static function(mixed $value) use (&$called): bool {
            ++$called;
            return is_string($value);
        };

        self::assertSame(false, IterableAlgorithms::all($testData, $predicate));
        self::assertSame(2, $called);
    }

    public static function dataForTestAll5(): iterable
    {
        yield from self::mixedIterables();
    }

    /** Ensure algorithm returns false when last item fails predicate. */
    #[DataProvider("dataForTestAll5")]
    public function testAll5(iterable $testData): void
    {
        $called = 0;

        // data is string,int,float
        $predicate = static function(mixed $value) use (&$called): bool {
            ++$called;
            return !is_float($value);
        };

        self::assertSame(false, IterableAlgorithms::all($testData, $predicate));
        self::assertSame(3, $called);
    }

    public static function dataForTestAllKeys1(): iterable
    {
        yield from self::nonEmptyIterables();
    }

    /** Ensure all sorts of iterables pass with a closure. */
    #[DataProvider("dataForTestAllKeys1")]
    public function testAllKeys1(iterable $testData): void
    {
        $called = false;

        $predicate = static function(mixed $value) use (&$called): bool {
            $called = true;
            return true;
        };

        self::assertSame(true, IterableAlgorithms::allKeys($testData, $predicate));
        self::assertTrue($called);
    }

    public static function dataForTestAllKeys2(): iterable
    {
        yield from self::nonEmptyIterables();
    }

    /** Ensure all sorts of iterables fail with a closure, and it's called only once. */
    #[DataProvider("dataForTestAllKeys2")]
    public function testAllKeys2(iterable $testData): void
    {
        $called = false;

        $predicate = static function(mixed $value) use (&$called): bool {
            IterableUtilitiesTest::assertFalse($called);
            $called = true;
            return false;
        };

        self::assertSame(false, IterableAlgorithms::allKeys($testData, $predicate));
        self::assertTrue($called);
    }

    public static function dataForTestAllKeys3(): iterable
    {
        yield from self::emptyIterables();
    }

    /** Ensure empty iterables pass without calling predicate. */
    #[DataProvider("dataForTestAllKeys3")]
    public function testAllKeys3(iterable $testData): void
    {
        $called = false;

        $predicate = static function(mixed $value) use (&$called): bool {
            IterableUtilitiesTest::fail("The predicate should not be called");
        };

        self::assertSame(true, IterableAlgorithms::allKeys($testData, $predicate));
        self::assertFalse($called);
    }

    public static function dataForTestAllKeys4(): iterable
    {
        yield from self::keyedIterables();
    }

    /** Ensure algorithm exits on first failed predicate call. */
    #[DataProvider("dataForTestAllKeys4")]
    public function testAllKeys4(iterable $testData): void
    {
        $called = 0;

        // keys are "mokkd", "meaning", 3
        $predicate = static function(mixed $key) use (&$called): bool {
            ++$called;
            return "meaning" !== $key;
        };

        self::assertSame(false, IterableAlgorithms::allKeys($testData, $predicate));
        self::assertSame(2, $called);
    }

    public static function dataForTestAllKeys5(): iterable
    {
        yield from self::keyedIterables();
    }

    /** Ensure algorithm returns false when last item fails predicate. */
    #[DataProvider("dataForTestAllKeys5")]
    public function testAllKeys5(iterable $testData): void
    {
        $called = 0;

        // keys are "mokkd", "meaning", 3
        $predicate = static function(mixed $key) use (&$called): bool {
            ++$called;
            return is_string($key);
        };

        self::assertSame(false, IterableAlgorithms::allKeys($testData, $predicate));
        self::assertSame(3, $called);
    }
}
