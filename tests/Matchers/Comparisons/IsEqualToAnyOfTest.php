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
    public const NotEqualConstraints = ["function", [99, 44, "mokkd"], 21, 3.142, "null"];

    private static function embedEqualConstraint(mixed $arg): array
    {
        static $location = 0;
        return [...array_slice(self::Constraints, 0, $location), $arg, ...array_slice(self::Constraints, ++$location)];
    }

    private static function embedNotEqualConstraint(mixed $arg): array
    {
        static $location = 0;
        return [...array_slice(self::NotEqualConstraints, 0, $location), $arg, ...array_slice(self::NotEqualConstraints, ++$location)];
    }

    public static function dataForTestMatches1(): iterable
    {
        foreach (DataFactory::equalValues() as $args) {
            # ensure we have datasets where the matching arg is anywhere within the constraint set
            yield [$args[0], ...self::embedEqualConstraint($args[1])];
        }

        foreach (DataFactory::identicalValues() as $args) {
            yield [$args[0], ...self::embedEqualConstraint($args[1])];
        }
    }

    /** Ensure values that are equal one of the constraint set match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(mixed $value, mixed ...$constraintSet): void
    {
        self::assertTrue((new IsEqualToAnyOf(...$constraintSet))->matches($value));
    }

    public static function dataForTestMatches2(): iterable
    {
        foreach (DataFactory::unequalValues() as $args) {
            yield [$args[0], ...self::embedNotEqualConstraint($args[1])];
        }
    }

    /** Ensure values that are not equal to any of the constraint set don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $value, mixed ...$constraintSet): void
    {
        self::assertFalse((new IsEqualToAnyOf($constraintSet))->matches($value));
    }

    /** Ensure identical objects match. */
    public function testMatches3(): void
    {
        $object = new class {};
        self::assertTrue((new IsEqualToAnyOf(self::embedEqualConstraint($object)))->matches($object));
    }

    /** Ensure equal objects match. */
    public function testMatches4(): void
    {
        $object = new class {};
        self::assertTrue((new IsEqualToAnyOf(self::embedEqualConstraint($object)))->matches(clone $object));
    }

    /** Ensure resources match like values. */
    public function testMatches5(): void
    {
        $resource = fopen("php://memory", "r");
        $equalResource = $resource;
        self::assertTrue((new IsEqualToAnyOf(self::embedEqualConstraint($resource)))->matches($equalResource));
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
