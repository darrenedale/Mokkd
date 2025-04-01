<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Comparisons;

use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Comparisons\IsEqualToAnyOf;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(IsEqualToAnyOf::class)]
class IsEqualToAnyOfTest extends TestCase
{
    public const Constraints = [null, "", 42, 3.1415927, [], true];

    // These values are guaranteed not to match any of the data yielded by unequalValues()
    public const NotEqualConstraints = ["function", [99, 44, "mokkd"], 21, 1.557, "null"];

    /** Embed an argument in the array of constraints when ensuring a value equal to it matches the array. */
    private static function embedEqualConstraint(mixed $arg): array
    {
        static $location = 0;
        return [...array_slice(self::Constraints, 0, $location), $arg, ...array_slice(self::Constraints, ++$location)];
    }

    /** Embed an argument in the array of non-equal constraints when ensuring a value not equal to it doesn't match the array. */
    private static function embedNotEqualConstraint(mixed $arg): array
    {
        static $location = 0;
        return [...array_slice(self::NotEqualConstraints, 0, $location), $arg, ...array_slice(self::NotEqualConstraints, ++$location)];
    }

    /** Provides values paired with arrays that contain at least one equal value. */
    public static function dataForTestMatches1(): iterable
    {
        foreach (DataFactory::equalValues() as $label => $args) {
            # ensure we have datasets where the matching arg is anywhere within the constraint set
            yield $label => [$args[0], ...self::embedEqualConstraint($args[1])];
        }
    }

    /** Ensure values that are equal to one of the constraint set match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(mixed $value, mixed ...$constraintSet): void
    {
        self::assertTrue((new IsEqualToAnyOf(...$constraintSet))->matches($value));
    }

    /** Provides individual values paired with arrays that contain no values equal to it. */
    public static function dataForTestMatches2(): iterable
    {
        foreach (DataFactory::unequalValues() as $label => $args) {
            [$value, $constraint] = $args;

            if (is_bool($value)) {
                // true will match many values for equality, so skip it here to avoid reducing NotEqualConstraints
                continue;
            }

            yield $label => [$value, ...self::embedNotEqualConstraint($constraint)];
        }
    }

    /** Ensure values that are not equal to any of the constraint set don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $value, mixed ...$constraintSet): void
    {
        self::assertFalse((new IsEqualToAnyOf(...$constraintSet))->matches($value));
    }

    /** Ensure true does not match unexpected values of other types. */
    public function testMatches3(): void
    {
        self::assertFalse((new IsEqualToAnyOf(0, 0.0, false, null, ""))->matches(true));
    }

    /** Ensure false does not match unexpected values of other types. */
    public function testMatches4(): void
    {
        self::assertFalse((new IsEqualToAnyOf(1, 0.0000001, true, "false"))->matches(false));
    }

    /** Ensure the serialiser is used to describe the matcher. */
    public function testDescribe1(): void
    {
        $expected = self::Constraints;

        $serialiser = new class($expected) implements SerialiserContract
        {
            private array $expected;

            public function __construct(array & $expected)
            {
                $this->expected =& $expected;
            }

            public function serialise(mixed $value): string
            {
                IsEqualToAnyOfTest::assertNotEmpty($this->expected);
                IsEqualToAnyOfTest::assertEquals(array_shift($this->expected), $value);

                return  "(test-" . get_debug_type($value) . ")";
            }
        };

        self::assertSame("== (test-null) || == (test-string) || == (test-int) || == (test-float) || == (test-array) || == (test-bool)", (new IsEqualToAnyOf(...$expected))->describe($serialiser));
        self::assertEmpty($expected);
    }
}
