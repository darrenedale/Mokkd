<?php

declare(strict_types=1);

namespace MokkdTests\Expectations;

use Mokkd\Contracts\Expectation as ExpectationContract;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Expectations\Any;
use Mokkd\Expectations\Expectation;
use Mokkd\Matchers\Callback;
use Mokkd\Matchers\Comparisons\IsEqualTo;
use Mokkd\Matchers\Comparisons\IsIdenticalTo;
use MokkdTests\CreatesMockSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(Expectation::class)]
class ExpectationTest extends TestCase
{
    use CreatesMockSerialiser;

    private const TestArgumentValue = "mokkd";

    /** Ensure any() returns an Any matcher. */
    public function testAny1(): void
    {
        self::assertInstanceOf(Any::class, Expectation::any());
    }

    /** Ensure matches() returns true for no args. */
    public function testMatches1(): void
    {
        self::assertTrue((new Expectation())->matches());
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "empty-one-int" => [[], [42]];
        yield "one-int-empty" => [[42], []];
        yield "one-int-two-ints" => [[42], [42, 15]];
        yield "two-ints-one-int" => [[42, 15], [42]];
        yield "two-ints-three-ints" => [[42, 15, 81], [42, 15]];
        yield "three-ints-two-ints" => [[42, 15], [42, 15, 81]];

        foreach (DataFactory::arrays() as $label => $expected) {
            if (0 === count($expected)) {
                continue;
            }

            $actual = $expected;
            array_shift($actual);
            yield $label => [$expected, $actual];
            yield "reverse-{$label}" => [$actual, $expected];
        }
    }

    /** Ensure matches() returns false for on arg count mismatch. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(array $expected, array $actualArgs): void
    {
        $matchers = array_map(static fn(mixed $value): MatcherContract => new IsEqualTo($value), $expected);
        $expectation = new Expectation(...$matchers);
        self::assertFalse($expectation->matches(...$actualArgs));
    }

    /** Ensure matches() on Expectation calls matches() for all Matchers up to the first that fails. */
    public function testMatches3(): void
    {
        $args = [];
        $called = [];
        $matchers = [];

        for ($idx = 0; $idx < 3; ++$idx ) {
            $called[] = false;
            $args[] = $idx;
            $matchers[] = new Callback(static function(mixed $arg) use ($idx, &$called): bool {
                ExpectationTest::assertSame($idx, $arg);
                ExpectationTest::assertFalse($called[$idx]);
                $called[$idx] = true;
                # second matcher indicates no match
                return 1 !== $idx;
            });
        }

        $expectation = new Expectation(...$matchers);
        self::assertFalse($expectation->matches(...$args));

        # first two should be called, third should not
        self::assertSame([true, true, false], $called);
    }

    public static function dataForTestIsSatisfied1(): iterable
    {
        yield "integer-0" => [0];
        yield from DataFactory::positiveIntegers();
    }

    /** Ensure isSatisfied() works with unlimited call counts. */
    #[DataProvider("dataForTestIsSatisfied1")]
    public function testIsSatisfied1(int $count): void
    {
        $expectation = new Expectation(new IsIdenticalTo($count));
        $expectation->setExpected(ExpectationContract::UnlimitedTimes);
        $expectation->setReturn(null);

        for ($idx = 0; $idx < $count; ++$idx) {
            $expectation->match($count);
        }

        self::assertTrue($expectation->isSatisfied());
    }

    public static function dataForTestIsSatisfied2(): iterable
    {
        yield from DataFactory::positiveIntegers();
    }

    /** Ensure isSatisfied() correctly verifies the call count. */
    #[DataProvider("dataForTestIsSatisfied2")]
    public function testIsSatisfied2(int $count): void
    {
        $expectation = new Expectation(new IsIdenticalTo($count));
        $expectation->setExpected($count);
        $expectation->setReturn(null);

        for ($idx = 0; $idx < $count; ++$idx) {
            self::assertFalse($expectation->isSatisfied());
            $expectation->match($count);
        }

        self::assertTrue($expectation->isSatisfied());
        $expectation->match($count);
        self::assertFalse($expectation->isSatisfied());
    }

    /** Ensure isSatisfied() correctly verifies a zero call count (i.e. never called). */
    public function testIsSatisfied3(): void
    {
        $expectation = new Expectation(new IsIdenticalTo(0));
        $expectation->setExpected(0);
        $expectation->setReturn(null);
        self::assertTrue($expectation->isSatisfied());
        $expectation->match(0);
        self::assertFalse($expectation->isSatisfied());
    }

    /** Ensure message() calls the provided serialiser for is arguments. */
    public function testMessage1(): void
    {
        $args = DataFactory::unboxSingle(DataFactory::mixedArray());
        $serialised = array_map(static fn(mixed $value) => gettype($value), $args);
        $matchers = array_map(static fn(mixed $value): MatcherContract => new IsEqualTo($value), $args);

        $expectation = new Expectation(...$matchers);
        $actual = $expectation->message(self::mockSerialiser($args, $serialised));

        self::assertStringStartsWith("(" . implode(", ", $serialised) . ")", $actual);
    }

    public static function dataForTestMessage2(): iterable
    {
        yield from DataFactory::positiveIntegers();
    }

    /** Ensure message() has the correct call counts. */
    #[DataProvider("dataForTestMessage2")]
    public function testMessage2(int $count): void
    {
        $expectation = new Expectation(new IsIdenticalTo(self::TestArgumentValue));
        $expectation->setExpected($count);
        $expectation->setReturn(null);
        $serialisation = "(string[" . strlen(self::TestArgumentValue) . "])\"" . self::TestArgumentValue . "\"";

        $serialiser = self::mockSerialiser(
            iterator_to_array(DataFactory::repeat($count, self::TestArgumentValue)),
            iterator_to_array(DataFactory::repeat($count, $serialisation)),
        );

        for ($idx = 0; $idx < ($count - 1); ++$idx) {
            self::assertSame(
                "({$serialisation}) expected to be called exactly {$count} time(s) but called {$idx} time(s)",
                $expectation->message($serialiser),
            );

            $expectation->match(self::TestArgumentValue);
        }

        // ... even when it should not be called at all
        $expectation->match(self::TestArgumentValue);

        self::assertSame(
            "({$serialisation}) expected to be called exactly {$count} time(s) but called {$count} time(s)",
            $expectation->message($serialiser),
        );
    }
}
