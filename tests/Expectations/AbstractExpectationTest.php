<?php

/*
 * Copyright 2025 Darren Edale
 *
 * This file is part of the Mokkd package.
 *
 * Mokkd is free software: you can redistribute it and/or modify
 * it under the terms of the Apache License v2.0.
 *
 * Mokkd is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * Apache License for more details.
 *
 * You should have received a copy of the Apache License v2.0
 * along with Mokkd. If not, see <http://www.apache.org/licenses/>.
 */

declare(strict_types=1);

namespace MokkdTests\Expectations;

use LogicException;
use Mokkd\Contracts\Expectation;
use Mokkd\Contracts\KeyMapper as KeyMapperContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Exceptions\ExpectationException;
use Mokkd\Expectations\AbstractExpectation;
use Mokkd\Expectations\ReturnMode;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(AbstractExpectation::class)]
class AbstractExpectationTest extends TestCase
{
    /** Generate an AbstractExpectation to test with. */
    private static function createExpectation(?ReturnMode $mode = ReturnMode::Value, mixed $returnValue = null, ?KeyMapperContract $mapper = null, ?callable $matches = null, ?callable $isSatisfied = null, ?callable $message = null): AbstractExpectation
    {
        $expectation = new class($matches, $isSatisfied, $message) extends AbstractExpectation
        {
            private $matchesFn;

            private $isSatisfiedFn;

            private $messageFn;

            public function __construct(?callable $matches = null, ?callable $isSatisfied = null, ?callable $message = null)
            {
                $this->matchesFn = $matches ?? static fn(mixed ...$args): bool => true;
                $this->isSatisfiedFn = $isSatisfied ?? static fn(): bool => true;
                $this->messageFn = $message ?? static fn(): string => "";
            }

            public function matches(...$args): bool
            {
                return ($this->matchesFn)(...$args);
            }

            public function isSatisfied(): bool
            {
                return ($this->isSatisfiedFn)();
            }

            public function message(SerialiserContract $serialiser): string
            {
                return ($this->messageFn)($serialiser);
            }
        };

        if ($mode !== null) {
            $expectation->setReturn($returnValue, $mode, $mapper);
        }

        return $expectation;
    }

    private static function validMatchCounts(): iterable
    {
        for ($expected = 0; $expected <= 100; ++$expected) {
            yield "{$expected}" => [$expected];
        }

        yield "unlimited" => [Expectation::UnlimitedTimes];
        yield "int-max" => [PHP_INT_MAX];
    }

    private static function returnValues(): iterable
    {
        yield "object" => [new class {}];
        yield "string" => ["mokkd"];
        yield "int" => [42];
        yield "float" => [3.1415927];
        yield "null" => [null];
        yield "true" => [true];
        yield "false" => [false];
        yield "array" => [[1, 2, 3]];
        yield "empty-array" => [[]];
        yield "resource" => [fopen("php://memory", "r")];
    }

    private static function returnArrays(): iterable
    {
        foreach (self::returnValues() as $label => $value) {
            yield "one-{$label}" => [$value];
        }

        yield "mixed" => [["mokkd", [], 3.1415927, null, 42, false, new class {}, [1, 2, 3], true, fopen("php://memory", "r")]];
    }

    /** Ensure match() throws if the arguments don't match. */
    public function testMatch1(): void
    {
        $expectation = self::createExpectation(matches: static fn(mixed ...$args): bool => false);
        $this->expectException(ExpectationException::class);
        $this->expectExceptionMessage("Expectation does not match arguments ((string[5]) \"mokkd\", (int) 42)");
        $expectation->match("mokkd", 42);
    }

    /** Ensure the assertion in match() fires when no return mode is set. */
    public function testMatch2(): void
    {
        $this->skipIfAssertionsDisabled();
        $expectation = self::createExpectation(mode: null);
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Can't match an expectation when it doesn't have a return mode set");
        $expectation->match(true, 3.1415926, null, "", [1, 2, 3]);
    }

    public static function dataForTestMatch3(): iterable
    {
        yield from self::returnValues();
    }

    /** Ensure match() returns the value unmodified when return mode is Value. */
    #[DataProvider("dataForTestMatch3")]
    public function testMatch3(mixed $returnValue): void
    {
        $expectation = self::createExpectation(returnValue: $returnValue);
        self::assertSame($returnValue, $expectation->match("mokkd", 3.1415927));
    }

    public static function dataForTestMatch4(): iterable
    {
        yield from self::returnValues();
    }

    /** Ensure match() calls the function with the provided args and returns its return value when mode is Callback. */
    #[DataProvider("dataForTestMatch4")]
    public function testMatch4(mixed $returnValue): void
    {
        $called = false;
        $callback = static function(mixed ...$args) use (&$called, $returnValue): mixed {
            AbstractExpectationTest::assertFalse($called);
            AbstractExpectationTest::assertSame([3.1415927, "mokkd", null, 42], $args);
            $called = true;
            return $returnValue;
        };

        $expectation = self::createExpectation(mode: ReturnMode::Callback, returnValue: $callback);
        self::assertSame($returnValue, $expectation->match(3.1415927, "mokkd", null, 42));
        self::assertTrue($called);
    }

    public static function dataForTestMatch5(): iterable
    {
        yield from self::returnArrays();
    }

    /** Ensure match() returns elements from the array in sequence when mode is Seqeuential. */
    #[DataProvider("dataForTestMatch5")]
    public function testMatch5(mixed $returnArray): void
    {
        $expectation = self::createExpectation(mode: ReturnMode::Sequential, returnValue: $returnArray);

        while (0 < count($returnArray)) {
            self::assertSame(array_shift($returnArray), $expectation->match(null, []));
        }
    }

    public static function dataForTestMatch6(): iterable
    {
        yield from self::returnArrays();
    }

    /** Ensure match() wraps back to the first element in the return array. */
    #[DataProvider("dataForTestMatch6")]
    public function testMatch6(mixed $returnArray): void
    {
        $expectedValues = [...$returnArray, ...$returnArray];
        $expectation = self::createExpectation(mode: ReturnMode::Sequential, returnValue: $returnArray);

        while (0 < count($expectedValues)) {
            self::assertSame(array_shift($expectedValues), $expectation->match(false, [1, 2, 3]));
        }
    }

    public static function dataForTestMatch7(): iterable
    {
        yield "one-string-key" => [["mokkd" => "func"], "mokkd", "func", 42, null];

        $object = new class {};
        yield "one-int-key" => [[1 => $object], 1, $object, $object, true, 3.1415927];

        $mixed = [1 => $object, "two" => null, "three" => 3.1415927, 4 => false, 6 => [], "seven" => 42, 8 => [1, 2, 3], 10 => true];
        $keys = array_keys($mixed);
        shuffle($keys);

        foreach ($keys as $key) {
            yield "mixed-{$key}" => [$mixed, $key, $mixed[$key], true, null, $object, 0, "mokkd"];
        }
    }

    /** Ensure match() returns the correct mapped values. */
    #[DataProvider("dataForTestMatch7")]
    public function testMatch7(array $map, int|string $key, mixed $expected, mixed ...$args): void
    {
        $mapper = new class($key, $args) implements KeyMapperContract
        {
            private int|string $key;

            private array $expectedArgs;

            public function __construct(int|string $key, array $expectedArgs)
            {
                $this->key = $key;
                $this->expectedArgs = $expectedArgs;
            }

            public function mapKey(...$args): string|int
            {
                AbstractExpectationTest::assertSame($this->expectedArgs, $args);
                return $this->key;
            }
        };

        $expectation = self::createExpectation(mode: ReturnMode::Mapped, returnValue: $map, mapper: $mapper);
        self::assertSame($expected, $expectation->match(...$args));
    }

    /** Ensure an unmapped key throws the expected exception. */
    public function testMatch8(): void
    {
        self::skipIfAssertionsDisabled();

        $mapper = new class implements KeyMapperContract
        {
            public function mapKey(...$args): string|int
            {
                return "doesn't-exist";
            }
        };

        $expectation = self::createExpectation(mode: ReturnMode::Mapped, returnValue: ["mokkd" => "func"], mapper: $mapper);
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Expected mapped key, found \"doesn't-exist\"");
        $expectation->match([], 42, false);
    }

    public static function dataForTestMatched1(): iterable
    {
        foreach (self::validMatchCounts() as $label => $args) {
            if (PHP_INT_MAX === $args[0] || Expectation::UnlimitedTimes === $args[0]) {
                continue;
            }

            yield "{$label}" => $args;
        }
    }

    /** Ensure the reported number of matches is correct. */
    #[DataProvider("dataForTestMatched1")]
    public function testMatched1(int $matches): void
    {
        $expectation = self::createExpectation();

        for ($idx = 0; $idx < $matches; ++$idx) {
            $expectation->match(true, new class {}, []);
        }

        self::assertSame($matches, $expectation->matched());
    }

    public static function dataForTestSetExpected1(): iterable
    {
        yield from self::validMatchCounts();
    }

    /** Ensure we can set and retrieve the expected match count. */
    #[DataProvider("dataForTestSetExpected1")]
    public function testSetExpected1(int $expected): void
    {
        $expectation = self::createExpectation();
        $expectation->setExpected($expected);
        self::assertSame($expected, $expectation->expected());
    }

    public static function dataForTestSetExpected2(): iterable
    {
        for ($expected = -1; $expected >= -100; --$expected) {
            if ($expected === Expectation::UnlimitedTimes) {
                continue;
            }

            yield "{$expected}" => [$expected];
        }

        yield "int-min" => [PHP_INT_MIN];
    }

    /** Ensure setExpected() assertion fires with an invalid count. */
    #[DataProvider("dataForTestSetExpected2")]
    public function testSetExpected2(int $expected): void
    {
        self::skipIfAssertionsDisabled();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Expected \$count >= 0 or == ExpectationContract::UnlimitedTimes, found {$expected}");
        self::createExpectation()->setExpected($expected);
    }

    public static function dataForTestSetReturn1(): iterable
    {
        yield from self::returnValues();
    }

    /** Ensure a fixed return value is the default. */
    #[DataProvider("dataForTestSetReturn1")]
    public function testSetReturn1(mixed $value): void
    {
        $expectation = self::createExpectation();
        $expectation->setReturn($value);
        self::assertSame($value, $expectation->match("mokkd"));
    }

    public static function dataForTestSetReturn2(): iterable
    {
        yield from self::returnValues();
    }

    /** Ensure we can set and retrieve a fixed return value. */
    #[DataProvider("dataForTestSetReturn2")]
    public function testSetReturn2(mixed $value): void
    {
        $expectation = self::createExpectation();
        $expectation->setReturn($value, ReturnMode::Value);
        self::assertSame($value, $expectation->match(3.1415927, null, [1, 2, 3]));
    }

    /** Ensure a callable is returned rather than called when the return mode Value. */
    public function testSetReturn3(): void
    {
        $expected = static fn(mixed ...$args): string => "mokkd";
        $expectation = self::createExpectation();
        $expectation->setReturn($expected);
        self::assertSame($expected, $expectation->match("func", false));
    }

    /** Ensure an array is returned rather than iterated when the return mode isn't Sequential. */
    public function testSetReturn4(): void
    {
        $expected = ["mokkd", 42, null, []];
        $expectation = self::createExpectation();
        $expectation->setReturn($expected);

        for ($idx = 0; $idx < count($expected); $idx++) {
            self::assertSame($expected, $expectation->match("func", false));
        }
    }

    /** Ensure a map is returned rather than examined when the return mode isn't Mapped. */
    public function testSetReturn5(): void
    {
        $expected = ["mokkd" => "func", 42 => null, "empty" => []];
        $expectation = self::createExpectation();

        $mapper = new class implements KeyMapperContract
        {

            public function mapKey(...$args): string|int
            {
                return "empty";
            }
        };

        $expectation->setReturn($expected, ReturnMode::Value, $mapper);

        for ($idx = 0; $idx < count($expected); $idx++) {
            self::assertSame($expected, $expectation->match("func", false));
        }
    }

    /** Ensure we can set a return callback that gets called. */
    public function testSetReturn6(): void
    {
        $called = false;

        $callback = static function (mixed ...$args) use (&$called): string {
            AbstractExpectationTest::assertSame([[1, 2, 3], [], null, "func"], $args);
            $called = true;
            return "mokkd";
        };

        $expectation = self::createExpectation();
        $expectation->setReturn($callback, ReturnMode::Callback);
        $actual =$expectation->match([1, 2, 3], [], null, "func");
        self::assertTrue($called);
        self::assertSame("mokkd", $actual);
    }

    public static function dataForTestSetReturn7(): iterable
    {
        function freeFunction(mixed ...$args): string
        {
            return "mokkd-test-free-function-name return-value";
        }

        $testClass = new class
        {
            public static function staticCallback(mixed ...$args): array
            {
                return [4, 6, 2];
            }

            public function callback(mixed ...$args): null
            {
                return null;
            }

            public function __invoke(mixed ...$args): int
            {
                return 42;
            }
        };

        yield "function-name" => ["\\MokkdTests\\Expectations\\freeFunction", "mokkd-test-free-function-name return-value"];
        yield "static-method-tuple" => [[$testClass::class, "staticCallback"], [4, 6, 2]];
        yield "instance-method-tuple" => [[$testClass, "callback"], null];
        yield "invokable" => [$testClass, 42];
        yield "closure" => [static fn(mixed ...$args): float => 3.1415927, 3.1415927];
    }

    /** Ensure we can use all sorts of callables. */
    #[DataProvider("dataForTestSetReturn7")]
    public function testSetReturn7(callable $callback, mixed $expected): void
    {
        $expectation = self::createExpectation();
        $expectation->setReturn($callback, ReturnMode::Callback);
        self::assertSame($expected, $expectation->match([], true, null, 42, "tree"));
    }

    public static function dataForTestSetReturn8(): iterable
    {
        yield from self::returnValues();
    }

    /** Ensure the assertion fires when we try to set the return mode to Callback with a non-callable. */
    #[DataProvider("dataForTestSetReturn8")]
    public function testSetReturn8(mixed $nonCallback): void
    {
        self::skipIfAssertionsDisabled();
        $expectation = self::createExpectation();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Expecting valid callable");
        $expectation->setReturn($nonCallback, ReturnMode::Callback);
    }

    /** Ensure we can set a sequential return array whose values get returned in sequence. */
    public function testSetReturn9(): void
    {
        $values = [1, 2, 5, "func", null, 77, false];
        $expectation = self::createExpectation();
        $expectation->setReturn($values, ReturnMode::Sequential);

        while (0 < count($values)) {
            self::assertSame(array_shift($values), $expectation->match("42", "mokkd", true));
        }
    }

    /** Ensure the assertion fires if we try to set a Sequential return with an empty array. */
    public function testSetReturn10(): void
    {
        self::skipIfAssertionsDisabled();
        $expectation = self::createExpectation();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Expecting valid array");
        $expectation->setReturn([], ReturnMode::Sequential);
    }

    /** Ensure the assertion fires if we try to set a Sequential return with an array that's not a list. */
    public function testSetReturn11(): void
    {
        self::skipIfAssertionsDisabled();
        $expectation = self::createExpectation();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Expecting valid array");
        $expectation->setReturn(["mokkd" => "func"], ReturnMode::Sequential);
    }

    /** Ensure the assertion fires if we try to set a Sequential return with a non-array value. */
    public function testSetReturn12(): void
    {
        self::skipIfAssertionsDisabled();
        $expectation = self::createExpectation();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Expecting valid array");
        $expectation->setReturn("4, 5, 6", ReturnMode::Sequential);
    }

    /** Ensure we can set a return map and get the correct mapped value. */
    public function testSetReturn13(): void
    {
        $map = ["mokkd" => "func", 2 => null, "five" => 5, "class" => new class {}, "int" => 77];
        $keys = array_keys($map);
        shuffle($keys);

        $mapper = new class($keys) implements KeyMapperContract
        {
            private array $keys;

            public function __construct(array $keys)
            {
                $this->keys = $keys;
            }

            public function mapKey(...$args): string|int
            {
                return array_shift($this->keys);
            }
        };

        $expectation = self::createExpectation();
        $expectation->setReturn($map, ReturnMode::Mapped, $mapper);

        while (0 < count($keys)) {
            self::assertSame($map[array_shift($keys)], $expectation->match("42", "mokkd", true));
        }
    }

    /** Ensure the assertion fires if we try to set a Mapped return mode with no mapper. */
    public function testSetReturn14(): void
    {
        self::skipIfAssertionsDisabled();
        $expectation = self::createExpectation();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("A Mapper must be provided when the return mode is Mapped");
        $expectation->setReturn(["mokkd" => "func"], ReturnMode::Mapped);
    }

    /** Ensure the assertion fires if we try to set a Mapped return mode with an empty map. */
    public function testSetReturn15(): void
    {
        self::skipIfAssertionsDisabled();
        $expectation = self::createExpectation();

        $mapper = new class implements KeyMapperContract
        {
            public function mapKey(...$args): string|int
            {
                return "mokkd";
            }
        };

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Expecting valid map");

        $expectation->setReturn([], ReturnMode::Mapped, $mapper);
    }

    /** Ensure the assertion fires if we try to set a Mapped return mode with a non-array. */
    public function testSetReturn16(): void
    {
        self::skipIfAssertionsDisabled();
        $expectation = self::createExpectation();

        $mapper = new class implements KeyMapperContract
        {
            public function mapKey(...$args): string|int
            {
                return "mokkd";
            }
        };

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Expecting valid map");

        $expectation->setReturn("1, 2, 3", ReturnMode::Mapped, $mapper);
    }
}
