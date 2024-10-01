<?php

namespace MokkdTests\Matchers\Integers;

use LogicException;
use Mokkd\Matchers\Integers\IsMultipleOf;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsMultipleOfTest extends TestCase
{
    use CreatesNullSerialiser;

    /** Ensure 0 is rejected as the factor constraint. */
    public function testConstructor1(): void
    {
        $this->skipIfAssertionsDisabled();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Expecting non-zero factor, found 0");
        new isMultipleOf(0);
    }

    public static function dataForTestMatches1(): iterable
    {
        // test with all factors between -20 and 20 (except 0 because division by 0 is an error; and -1 and 1 because
        // every int is a multiple of both)
        foreach (DataFactory::concatenate(DataFactory::negativeIntegers(-20), DataFactory::positiveIntegers(20)) as $factorLabel => $factor) {
            $factor = DataFactory::unboxSingle($factor);

            if (1 === abs($factor)) {
                continue;
            }

            // the test value is $factor * n + 1, where -20 <= n <= 20
            foreach (DataFactory::transform(
                DataFactory::concatenate(DataFactory::negativeIntegers(-20), DataFactory::positiveIntegers(20), DataFactory::integerZero()),
                static fn (int $value): int => ($value * $factor) + 1,
            ) as $timesLabel => $testValue) {
                $testValue = DataFactory::unboxSingle($testValue);
                yield "{$factorLabel}-factor-times-{$timesLabel}-plus-one" => [$factor, $testValue];
            }
        }
    }

    /** Ensure non-multiple ints don't match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(int $factor, int $testValue): void
    {
        self::assertFalse((new IsMultipleOf($factor))->matches($testValue), "Expected {$testValue} not to be a multiple of {$factor}");
    }

    public static function dataForTestMatches2(): iterable
    {
        // test with all factors between -20 and 20 (except 0, because division by 0 is an error)
        foreach (DataFactory::concatenate(DataFactory::positiveIntegers(20), DataFactory::negativeIntegers(-20)) as $factorLabel => $factor) {
            $factor = DataFactory::unboxSingle($factor);

            // the test value is $factor * n, where -20 <= n <= 20
            foreach (DataFactory::transform(
                DataFactory::concatenate(DataFactory::negativeIntegers(-20), DataFactory::positiveIntegers(20), DataFactory::integerZero()),
                static fn (int $value): int => $value * $factor,
            ) as $timesLabel => $testValue) {
                $testValue = DataFactory::unboxSingle($testValue);
                yield "{$factorLabel}-factor-times-{$timesLabel}" => [$factor, $testValue];
            }
        }
    }

    /** Ensure ints that are multiples match successfully. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(int $factor, int $testValue): void
    {
        self::assertTrue((new IsMultipleOf($factor))->matches($testValue));
    }

    public static function dataForTestMatches3(): iterable
    {
        // test with all factors between -20 and 20 (except 0, because division by 0 is an error)
        foreach (DataFactory::concatenate(DataFactory::positiveIntegers(20), DataFactory::negativeIntegers(-20)) as $factorLabel => $factor) {
            $factor = DataFactory::unboxSingle($factor);

            // the test value is $factor * n, where -20 <= n <= 20
            foreach (DataFactory::transform(
                DataFactory::concatenate(DataFactory::negativeIntegers(-20), DataFactory::positiveIntegers(20), DataFactory::integerZero()),
                static fn (int $value): float => (float) ($value * $factor),
            ) as $timesLabel => $testValue) {
                $testValue = DataFactory::unboxSingle($testValue);
                yield "{$factorLabel}-factor-times-{$timesLabel}" => [$factor, $testValue];
            }
        }
    }

    /** Ensure floats that are multiples don't match. */
    #[DataProvider("dataForTestMatches3")]
    public function testMatches3(int $factor, float $testValue): void
    {
        // first, prove that we're testing with a value consistent with the purpose of the test
        self::assertEquals((int) $testValue, $testValue, "{$testValue} != (int) {$testValue} (" . ((int) $testValue) . ")");
        $matcher = new IsMultipleOf($factor);
        self::assertTrue($matcher->matches((int) $testValue), "Integers\\IsMultipleOf($factor}) does not match (int) {$testValue} (" . ((int) $testValue) . ")");

        // then prove the float value doesn't match
        self::assertFalse($matcher->matches($testValue));
    }

    public static function dataForTestDescribe1(): iterable
    {
        yield "positive-1" => [1, "(int) {multiple of 1}"];
        yield "positive-8" => [8, "(int) {multiple of 8}"];
        yield "negative-minus-1" => [-1, "(int) {multiple of -1}"];
        yield "positive-minus-8" => [-8, "(int) {multiple of -8}"];
    }

    /** Ensure the matcher describes itself as expected. */
    #[DataProvider("dataForTestDescribe1")]
    public function testDescribe1(int $factor, string $expected): void
    {
        self::assertSame($expected, (new IsMultipleOf($factor))->describe(self::nullSerialiser()));
    }
}
