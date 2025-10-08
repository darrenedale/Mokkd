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

namespace MokkdTests\Matchers;

use Mokkd\Matchers\Callback;
use Mokkd\Utilities\Guard;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

use function uopz_set_hook;
use function uopz_unset_hook;

#[CoversClass(Callback::class)]
class CallbackTest extends TestCase
{
    use CreatesNullSerialiser;

    private const ExpectedValue = "callback-test-matching-value";

    private static bool $called;

    public function setUp(): void
    {
        self::$called = false;
    }

    public function tearDown(): void
    {
        self::$called = false;
    }

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::callables(static fn() => self::$called = true, true);
    }

    /** Ensure the constructor can set all types of callable. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(callable $callable): void
    {
        $callback = new Callback($callable);
        $callback->matches(null);
        self::assertTrue(self::$called);
    }

    public static function dataForTestMatches2(): iterable
    {
        $callback = function(mixed ...$args): void {
            CallbackTest::assertCount(1, $args);
            $value = $args[0];
            CallbackTest::assertSame(self::ExpectedValue, $value);
            CallbackTest::$called = true;
        };

        yield from DataFactory::callables($callback, true);
    }

    /** Ensure the expectation passes on the correct arguments to the callback. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $callable): void
    {
        $callback = new Callback($callable);
        $callback->matches(self::ExpectedValue);
        self::assertTrue(self::$called);
    }

    public static function dataForTestMatches3(): iterable
    {
        foreach (DataFactory::callables(return: true) as $label => $args) {
            yield "{$label}->true" => [...$args, true];
        }

        foreach (DataFactory::callables(return: false) as $label => $args) {
            yield "{$label}->false" => [...$args, false];
        }
    }

    /** Ensure the expectation returns the result of the callback. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(mixed $callable, bool $expectedResult): void
    {
        $callback = new Callback($callable);
        self::assertSame($expectedResult, $callback->matches(self::ExpectedValue));
    }

    public static function dataForTestMatches4(): iterable
    {
        yield "free-function-returns-true" => ["is_string", true];
        yield "free-function-returns-false" => ["is_float", false];
    }

    /** Ensure the expectation can be used with free functions. */
    #[DataProvider("dataForTestMatches4")]
    public function testMatches4(string $functionName, bool $expected): void
    {
        // actual hook is called outside this scope so we delegate to a captured closure
        $setCalled = fn() => self::$called = true;
        self::assertTrue(uopz_set_hook($functionName, fn() => $setCalled()));
        $guard = new Guard(static fn () => uopz_unset_hook($functionName));

        self::assertSame($expected, (new Callback($functionName))->matches(self::ExpectedValue));
        self::assertTrue(self::$called);
    }

    public static function dataForTestMatches5(): iterable
    {
        yield from DataFactory::booleans();
    }

    /** Ensure the expectation can be used with static methods. */
    #[DataProvider("dataForTestMatches5")]
    public function testMatches5(bool $returnValue): void
    {
        $setCalled = fn() => self::$called = true;

        $object = new class(self::ExpectedValue, $returnValue, $setCalled)
        {
            private static string $expectedValue;

            private static bool $returnValue;

            private static $setCalled;

            public function __construct(string $value, bool $returnValue, callable $setCalled)
            {
                self::$expectedValue = $value;
                self::$returnValue = $returnValue;
                self::$setCalled = $setCalled;
            }

            public static function testMethod(mixed $arg): bool
            {
                CallbackTest::assertSame(self::$expectedValue, $arg);
                (self::$setCalled)();
                return self::$returnValue;
            }
        };

        self::assertSame($returnValue, (new Callback([$object::class, "testMethod"]))->matches(self::ExpectedValue));
        self::assertTrue(self::$called);
    }

    /** Ensure the matcher describes itself as expected. */
    public function testDescribe1(): void
    {
        self::assertSame("(callback matcher)", (new Callback(fn() => true))->describe(self::nullSerialiser()));
    }
}
