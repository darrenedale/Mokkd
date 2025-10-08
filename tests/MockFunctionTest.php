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

namespace MokkdTests;

use Equit\XRay\StaticXRay;
use Equit\XRay\XRay;
use LogicException;
use Mokkd\Contracts\Expectation as ExpectationContract;
use Mokkd\Contracts\KeyMapper as KeyMapperContract;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Exceptions\ExpectationException;
use Mokkd\Exceptions\FunctionException;
use Mokkd\Exceptions\UnexpectedFunctionCallException;
use Mokkd\Expectations\AbstractExpectation;
use Mokkd\Expectations\Any as AnyExpectation;
use Mokkd\Expectations\ReturnMode;
use Mokkd\Mappers\IndexedArgument;
use Mokkd\Matchers\Any;
use Mokkd\Matchers\Comparisons\IsEqualTo;
use Mokkd\Matchers\Comparisons\IsIdenticalTo;
use Mokkd\Matchers\Strings\BeginsWith;
use Mokkd\Matchers\Types\IsNull;
use Mokkd\Matchers\Types\IsString;
use Mokkd\MockFunction;
use Mokkd\Utilities\Serialiser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

#[CoversClass(MockFunction::class)]
class MockFunctionTest extends TestCase
{
    private MockFunction $mock;

    private SerialiserContract $serialiser;

    // The mocks created by the test that will be uninstalled during tearDown()
    private array $mocks;

    public function setUp(): void
    {
        $this->serialiser = new Serialiser();
        $this->mock = new MockFunction("time", $this->serialiser);
        $this->mocks = [];

        // The constructor installs the mock, we uninstall it because we want to control when and how the mock is used
        $this->mock->uninstall();
    }

    public function tearDown(): void
    {
        $this->mock->uninstall();
        
        foreach ($this->mocks as $mock) {
            $mock->uninstall();
        }

        unset($this->mock, $this->mocks);
    }

    /** Create a MockFunction instance and add it to the set that will be cleaned up in tearDown(). */
    private function createMockFunction(string $fn, SerialiserContract $serialiser): MockFunction
    {
        $mock = new MockFunction($fn, $serialiser);
        $this->mocks[] = $mock;
        return $mock;
    }

    /** Helper to create a non-default Serialiser with some useful testing telemetry. */
    private static function createSerialiser(): SerialiserContract
    {
        return new class implements SerialiserContract
        {
            public int $count = 0;

            public array $calls = [];

            public function serialise(mixed $value): string
            {
                if (!array_key_exists($value, $this->calls)) {
                    $this->calls[$value] = 1;
                } else {
                    $this->calls[$value] += 1;
                }

                ++$this->count;
                return "serialisation #{$this->count}";
            }
        };
    }

    /** A selection of non-existent and/or invalid function names to test various scenarios. */
    public static function badFunctionNames(): iterable
    {
        yield "empty" => [""];
        yield "whitespace" => ["  "];
        yield "leading-whitespace" => [" time"];
        yield "trailing-whitespace" => ["time "];
        yield "surrounding-whitespace" => [" time "];
        yield "internal-whitespace" => ["array keys"];
        yield "non-existent" => ["non_existent_function"];
        yield "invalid-character" => ["array-keys"];
    }

    /** A selection of values to test when creating an argument matcher. */
    public static function matcherValues(): iterable
    {
        yield "empty-string" => [""];
        yield "string" => ["life"];
        yield "int" => [42];
        yield "float" => [3.14];
        yield "null" => [null];
        yield "array" => [[0, "one", 2.0]];
        yield "object" => [new StdClass()];
        yield "resource" => [fopen("php://memory", "r")];
    }

    /** A selection of matchers to test with when creating an argument matcher. */
    public static function matchers(): iterable
    {
        yield "IsIdenticalTo-int" => [new IsIdenticalTo(42)];
        yield "IsIdenticalTo-float" => [new IsIdenticalTo(3.14)];
        yield "IsEqualTo-int" => [new IsEqualTo(42)];
        yield "IsEqualTo-float" => [new IsEqualTo(3.14)];
        yield "IsString" => [new IsString()];
        yield "IsNull" => [new IsNull()];
        yield "BeginsWith" => [new BeginsWith("mokkd-")];
        yield "Any" => [new Any()];
    }

    /** A sample of functions that return void. */
    public static function voidFunctions(): iterable
    {
        yield "udf-namespaced" => ["MokkdTests\\namespacedVoidFunction"];
        yield "udf-root" => ["rootNamespacedVoidFunction"];
        yield "built-in" => ["header"];
    }

    /** A sample of functions that don't return void. */
    public static function nonVoidFunctions(): iterable
    {
        yield "udf-namespaced" => ["MokkdTests\\namespacedFunction"];
        yield "udf-root" => ["rootNamespacedFunction"];
        yield "built-in" => ["is_array"];
    }

    /** Ensure we can create a mock for the provided function with the provided serialiser. */
    public function testConstructor1(): void
    {
        $serialiser = new Serialiser();
        $actual = $this->createMockFunction("time", $serialiser);
        self::assertSame("time", $actual->functionName());
        self::assertSame($serialiser, $actual->serialiser());
    }

    /** Ensure we can create a mock with a namespaced function name. */
    public function testConstructor2(): void
    {
        $actual = $this->createMockFunction("MokkdTests\\namespacedFunction", new Serialiser());
        self::assertSame("MokkdTests\\namespacedFunction", $actual->functionName());
    }

    /** Ensure we can create a mock with a custom serialiser. */
    public function testConstructor3(): void
    {
        $serialiser = self::createSerialiser();
        $actual = $this->createMockFunction("MokkdTests\\namespacedFunction", $serialiser);
        self::assertSame($serialiser, $actual->serialiser());
    }

    /** Ensure the constructor installs the mock. */
    public function testConstructor4(): void
    {
        $this->createMockFunction("time", new Serialiser());

        // the constructor installs the mock, but no expectations have been set, so...
        $this->expectException(UnexpectedFunctionCallException::class);
        $this->expectExceptionMessage("No matching expectation found for function call time()");
        time();
    }

    /** Ensure the constructor throws when the function name starts with a backslash. */
    public function testConstructor5(): void
    {
        $this->expectException(FunctionException::class);
        $this->expectExceptionMessage("Expected valid function name, found \"\\time\"");
        new MockFunction("\\time", new Serialiser());
    }

    /** Ensure the constructor throws when the function doesn't exist. */
    #[DataProvider("badFunctionNames")]
    public function testConstructor6(string $name): void
    {
        $this->expectException(FunctionException::class);
        $this->expectExceptionMessage("Expected valid function name, found \"{$name}\"");
        new MockFunction($name, new Serialiser());
    }

    /** Ensure destructor invokes uninstall(). */
    public function testDestructor1(): void
    {
        $tracker = new StdClass();
        $tracker->count = 0;

        $mock = new class($tracker) extends MockFunction
        {
            private StdClass $tracker;

            public function __construct(StdClass $tracker)
            {
                parent::__construct("MokkdTests\\namespacedFunction", new Serialiser());
                $this->tracker = $tracker;
            }

            public function uninstall(): void
            {
                ++$this->tracker->count;
                parent::uninstall();
            }
        };

        $mock->__destruct();
        self::assertSame(1, $tracker->count);
    }

    /** Ensure argument matcher helper produces IsIdenticalTo matchers when given values. */
    #[DataProvider("matcherValues")]
    public function testCreateMatcher1(mixed $value): void
    {
        $actual = (new StaticXRay(MockFunction::class))->createMatcher($value);
        self::assertInstanceOf(IsIdenticalTo::class, $actual);
        self::assertSame($value, (new XRay($actual))->expected);
    }

    /** Ensure argument matcher helper uses the provided matcher. */
    #[DataProvider("matchers")]
    public function testCreateMatcher2(MatcherContract $matcher): void
    {
        $actual = (new StaticXRay(MockFunction::class))->createMatcher($matcher);
        self::assertSame($matcher, $actual);
    }

    /** Ensure install() sets the mock. */
    public function testInstall1(): void
    {
        $expected = new StdClass();

        // ensure the mock isn't installed yet
        self::assertIsInt(time());

        $this->mock->returning($expected);
        $this->mock->install();
        self::assertSame($expected, time());
    }

    #[DataProvider("voidFunctions")]
    public function testIsVoid1(string $fn): void
    {
        self::assertTrue((new XRay($this->createMockFunction($fn, new Serialiser())))->isVoid());
    }

    #[DataProvider("nonVoidFunctions")]
    public function testIsVoid2(string $fn): void
    {
        self::assertFalse((new XRay($this->createMockFunction($fn, new Serialiser())))->isVoid());
    }

    /** Ensure invokeOriginal() invokes the original function instead of the mock. */
    public function testInvokeOriginal1(): void
    {
        $this->mock->returning("mokkd")->install();
        self::assertIsInt((new XRay($this->mock))->invokeOriginal());
    }

    /** Ensure invokeOriginal() invokes a void original function instead of the mock. */
    public function testInvokeOriginal2(): void
    {
        $mock = $this->createMockFunction("rootNamespacedVoidFunctionWithSideEffects", new Serialiser())
            ->returningVoid();

        // The side effect is that this object's value property will be set to "mokkd"
        $actual = new StdClass();
        $actual->value = "";
        (new XRay($mock))->invokeOriginal($actual);
        self::assertSame("mokkd", $actual->value);
    }

    /** Ensure invokeOriginal() re-installs the mock. */
    public function testInvokeOriginal3(): void
    {
        $this->mock->returning("mokkd")->install();
        (new XRay($this->mock))->invokeOriginal();
        self::assertSame("mokkd", time());
    }

    /** Ensure invokeOriginal() re-installs the mock even when an exception occurs inside the original. */
    public function testInvokeOriginal4(): void
    {
        $this->mock->returning("mokkd")->install();
        (new XRay($this->mock))->invokeOriginal();
        self::assertSame("mokkd", time());
    }

    /** Ensure the default expectation is an any returning void. */
    public function testCurrentExpectation1(): void
    {
        /** @var ExpectationContract $actual */
        $actual = (new XRay($this->mock))->currentExpectation();
        self::assertInstanceOf(AnyExpectation::class, $actual);
        self::assertSame((new XRay($actual))->returnMode, ReturnMode::Void);
    }

    /** Ensure we can fetch the name of the mocked function. */
    public function testFunctionName1(): void
    {
        self::assertSame("time", $this->mock->functionName());
    }

    /** Ensure we can fetch the name of a namespaced function. */
    public function testFunctionName2(): void
    {
        $mock = new MockFunction("MokkdTests\\namespacedFunction", $this->serialiser);
        self::assertSame("MokkdTests\\namespacedFunction", $mock->functionName());
    }

    /** Ensure we can fetch the mock function's serialiser. */
    public function testSerialiser1(): void
    {
        self::assertSame($this->serialiser, $this->mock->serialiser());
    }

    /** Ensure we can create a new expectation. */
    public function testExpects1(): void
    {
        $mock = new XRay($this->mock);
        $first = $mock->currentExpectation();
        $actual = $mock->expects();
        $second = $mock->currentExpectation();
        self::assertSame($this->mock, $actual);
        self::assertNotSame($first, $second);
    }

    /** Ensure once() sets the expectation to be called one time. */
    public function testOnce1(): void
    {
        $mock = new XRay($this->mock);
        $actual = $mock->expects()->once();
        $expectation = new XRay($mock->currentExpectation());
        self::assertSame($this->mock, $actual);
        self::assertSame(1, $expectation->expectedCount);
    }

    /** Ensure twice() sets the expectation to be called two times. */
    public function testTwice1(): void
    {
        $mock = new XRay($this->mock);
        $actual = $mock->expects()->twice();
        $expectation = new XRay($mock->currentExpectation());
        self::assertSame($this->mock, $actual);
        self::assertSame(2, $expectation->expectedCount);
    }

    /** Ensure twice() sets the expectation to be called two times. */
    public function testNever1(): void
    {
        $mock = new XRay($this->mock);
        $actual = $mock->expects()->never();
        $expectation = new XRay($mock->currentExpectation());
        self::assertSame($this->mock, $actual);
        self::assertSame(0, $expectation->expectedCount);
    }

    /** Data provider with valid integral arguments for times(). */
    public static function providerTestTimes1(): iterable
    {
        for ($times = 0; $times < 11; ++$times) {
            yield "times-{$times}" => [$times];
        }
    }

    /** Ensure we can set the number of times a call is expected. */
    #[DataProvider("providerTestTimes1")]
    public function testTimes1(int $times): void
    {
        $mock = new XRay($this->mock);
        $actual = $mock->expects()->times($times);
        $expectation = new XRay($mock->currentExpectation());
        self::assertSame($this->mock, $actual);
        self::assertSame($times, $expectation->expectedCount);
    }

    /** Ensure we can set a call to be expected an unlimited number of times. */
    public function testTimes2(): void
    {
        $mock = new XRay($this->mock);
        $actual = $mock->expects()->times(ExpectationContract::UnlimitedTimes);
        $expectation = new XRay($mock->currentExpectation());
        self::assertSame($this->mock, $actual);
        self::assertSame(ExpectationContract::UnlimitedTimes, $expectation->expectedCount);
    }

    /** Data provider with valid integral arguments for times(). */
    public static function providerTestTimes3(): iterable
    {
        for ($times = -1; $times > -11; --$times) {
            if ($times === ExpectationContract::UnlimitedTimes) {
                continue;
            }

            yield "invalid-times-{$times}" => [$times];
        }
    }

    /** Ensure times() throws with invalid call count expectations. */
    #[DataProvider("providerTestTimes3")]
    public function testTimes3(int $times): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Expected \$count >= 0 or == ExpectationContract::UnlimitedTimes, found {$times}");
        $this->mock->expects()->times($times);
    }

    /** Data provider with some valid return values for testReturning1(). */
    public static function providerReturnValues(): iterable
    {
        yield "string" => ["mokkd"];
        yield "int" => [42];
        yield "float" => [3.1415926];
        yield "bool-true" => [true];
        yield "bool-false" => [false];
        yield "null" => [null];
        yield "object" => [new stdClass()];
        yield "array" => [[1, 2, 3,],];
    }

    /** Ensure a static return value can be set. */
    #[DataProvider("providerReturnValues")]
    public function testReturning1(mixed $value): void
    {
        $actual = $this->mock->expects()->returning($value);
        $expectation = new XRay((new XRay($this->mock))->currentExpectation());
        self::assertSame(ReturnMode::Value, $expectation->returnMode);
        self::assertSame($this->mock, $actual);
        self::assertSame($value, $expectation->returnValue);
    }

    /** Ensure we can set an expectation to return void. */
    public function testReturningVoid1(): void
    {
        $actual = $this->mock->expects()->returningVoid();
        $expectation = new XRay((new XRay($this->mock))->currentExpectation());
        self::assertSame($this->mock, $actual);
        self::assertSame(ReturnMode::Void, $expectation->returnMode);
    }

    /** Ensure we can set an expectation to return from a sequence of values. */
    public function testReturningFrom1(): void
    {
        $actual = $this->mock->expects()->returningFrom([1, 2, 3,]);
        $expectation = new XRay((new XRay($this->mock))->currentExpectation());
        self::assertSame($this->mock, $actual);
        self::assertSame(ReturnMode::Sequential, $expectation->returnMode);
        self::assertSame([1, 2, 3,], $expectation->returnValue);
    }

    /** Ensure we can set an expectation to return a mapped value based on a fixed positional input argument. */
    public function testReturningMappedValueFrom1(): void
    {
        $returns = [
            "mokkd" => "function",
            "vendor" => "citruslab",
            "library" => "mokkd",
        ];

        $actual = $this->mock->expects()->returningMappedValueFrom($returns, 1);
        $expectation = new XRay((new XRay($this->mock))->currentExpectation());
        self::assertSame($this->mock, $actual);
        self::assertSame(ReturnMode::Mapped, $expectation->returnMode);
        self::assertSame($returns, $expectation->returnValue);
        self::assertInstanceOf(IndexedArgument::class, $expectation->mapper);
        self::assertSame(1, $expectation->mapper->index());
    }

    /** Ensure we can set an expectation to return a mapped value using a custom mapper. */
    public function testReturningMappedValueFrom2(): void
    {
        $returns = [
            "mokkd" => "function",
            "vendor" => "citruslab",
            "library" => "mokkd",
        ];

        $mapper = new class implements KeyMapperContract
        {
            public function mapKey(...$args): string|int
            {
                return "mokkd";
            }
        };

        $actual = $this->mock->expects()->returningMappedValueFrom($returns, $mapper);
        $expectation = new XRay((new XRay($this->mock))->currentExpectation());
        self::assertSame($this->mock, $actual);
        self::assertSame(ReturnMode::Mapped, $expectation->returnMode);
        self::assertSame($returns, $expectation->returnValue);
        self::assertSame($mapper, $expectation->mapper);
    }

    /** Ensure we can set an expectation to return using a callable. */
    public function testReturningUsing1(): void
    {
        $fn = static fn () => 42;
        $actual = $this->mock->expects()->returningUsing($fn);
        $expectation = new XRay((new XRay($this->mock))->currentExpectation());
        self::assertSame($this->mock, $actual);
        self::assertSame(ReturnMode::Callback, $expectation->returnMode);
        self::assertSame($fn, $expectation->returnValue);
    }

    /** Ensure the mock can be set to block unmatched calls. */
    public function testBlocking1(): void
    {
        $actual = $this->mock->blocking();
        self::assertSame($this->mock, $actual);
        self::assertTrue((new XRay($this->mock))->blocking);
    }

    /** Ensure the mock can be set to pass through unmatched calls. */
    public function testWithoutBlocking1(): void
    {
        $actual = $this->mock->withoutBlocking();
        self::assertSame($this->mock, $actual);
        self::assertFalse((new XRay($this->mock))->blocking);
    }

    /** Ensure the mock can be set to consume expectations as they are matched. */
    public function testConsuming1(): void
    {
        $actual = $this->mock->consuming();
        self::assertSame($this->mock, $actual);
        self::assertTrue((new XRay($this->mock))->consuming);
    }

    /** Ensure the mock can be set not to consume expectations as they are matched. */
    public function testWithoutConsuming1(): void
    {
        $actual = $this->mock->withoutConsuming();
        self::assertSame($this->mock, $actual);
        self::assertFalse((new XRay($this->mock))->consuming);
    }

    /** Ensure the mock can be uninstalled. */
    public function testUninstall1(): void
    {
        $this->mock->expects()->returning(1234);

        // first install the mock and prove that it's installed
        $this->mock->install();
        self::assertSame(1234, time());

        // then uninstall it and prove that it's not installed
        $this->mock->uninstall();
        self::assertNotEquals(1234, time());
    }

    /** Ensure we can add an arbitrary expectation. */
    public function testAddExpectation1(): void
    {
        $expectation = new AnyExpectation();
        $actual = $this->mock->addExpectation($expectation);
        self::assertSame($this->mock, $actual);
        $expectations = $this->mock->expectations();
        self::assertCount(1, $expectations);
        self::assertSame($expectation, $expectations[0]);
    }

    /** Ensure the mock has no expectations by default. */
    public function testExpectations1(): void
    {
        self::assertCount(0, $this->mock->expectations());
    }

    /** Ensure the mock has the correct expectations. */
    public function testExpectations2(): void
    {
        $mock = new XRay($this->mock);

        $actual = $mock->expects()->returningVoid();
        self::assertSame($this->mock, $actual);
        $first = $mock->currentExpectation();

        $actual = $mock->expects()->returning(42);
        self::assertSame($this->mock, $actual);
        $second = $mock->currentExpectation();

        $expectations = $this->mock->expectations();
        self::assertCount(2, $expectations);
        self::assertContainsOnlyInstancesOf(ExpectationContract::class, $expectations);
        self::assertSame($first, $expectations[0]);
        self::assertSame($second, $expectations[1]);
    }

    /** Ensure we throw if an expectation isn't satisfied. */
    public function testVerifyExpectations1(): void
    {
        $this->mock->addExpectation(new class extends AbstractExpectation
        {
            public function matches(...$args): bool
            {
                return true;
            }

            public function isSatisfied(): bool
            {
                return true;
            }

            public function message(SerialiserContract $serialiser): string
            {
                return " test expectation should be satisfied";
            }
        });

        $this->mock->addExpectation(new class extends AbstractExpectation
        {
            public function matches(...$args): bool
            {
                return true;
            }

            public function isSatisfied(): bool
            {
                return false;
            }

            public function message(SerialiserContract $serialiser): string
            {
                return " test expectation not satisfied";
            }
        });

        self::expectException(ExpectationException::class);
        self::expectExceptionMessage("time test expectation not satisfied");
        $this->mock->verifyExpectations();
    }

    /** Ensure we don't throw if all expectations are satisfied. */
    public function testVerifyExpectations2(): void
    {
        $expectation = new class extends AbstractExpectation
        {
            public function matches(...$args): bool
            {
                return true;
            }

            public function isSatisfied(): bool
            {
                return true;
            }

            public function message(SerialiserContract $serialiser): string
            {
                return " test expectation not satisfied";
            }
        };

        $this->mock->addExpectation($expectation);
        $this->mock->addExpectation($expectation);
        $this->mock->verifyExpectations();

        // this test passes if no exception is thrown
        self::markTestPassedWithoutAssertions();
    }
}
