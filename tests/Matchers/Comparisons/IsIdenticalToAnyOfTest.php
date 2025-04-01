<?php

declare(strict_types=1);

namespace MokkdTests\Matchers\Comparisons;

use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Comparisons\IsIdenticalToAnyOf;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(IsIdenticalToAnyOf::class)]
class IsIdenticalToAnyOfTest extends TestCase
{
    public const Constraints = [null, "", 42, 3.1415927, [], true];

    // These values are guaranteed not to match any of the data yielded by nonIdenticalValues()
    public const NotIdenticalConstraints = ["function", [99, 44, "mokkd"], 21, 1.557, "null"];

    private static function embedIdenticalConstraint(mixed $arg): array
    {
        static $location = 0;
        return [...array_slice(self::Constraints, 0, $location), $arg, ...array_slice(self::Constraints, ++$location)];
    }

    private static function embedNotIdenticalConstraint(mixed $arg): array
    {
        static $location = 0;
        return [...array_slice(self::NotIdenticalConstraints, 0, $location), $arg, ...array_slice(self::NotIdenticalConstraints, ++$location)];
    }

    public static function dataForTestMatches1(): iterable
    {
        foreach (DataFactory::identicalValues() as $label => $args) {
            yield $label => [$args[0], ...self::embedIdenticalConstraint($args[1])];
        }
    }

    /** Ensure values that are equal to one of the constraint set match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(mixed $value, mixed ...$constraintSet): void
    {
        self::assertTrue((new IsIdenticalToAnyOf(...$constraintSet))->matches($value));
    }

    public static function dataForTestMatches2(): iterable
    {
        foreach (DataFactory::nonIdenticalValues() as $label => $args) {
            yield $label => [$args[0], ...self::embedNotIdenticalConstraint($args[1])];
        }
    }

    /** Ensure values that are not identical to any of the constraint set don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $value, mixed ...$constraintSet): void
    {
        self::assertFalse((new IsIdenticalToAnyOf(...$constraintSet))->matches($value));
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
                IsIdenticalToAnyOfTest::assertNotEmpty($this->expected);
                IsIdenticalToAnyOfTest::assertEquals(array_shift($this->expected), $value);

                return  "(test-" . get_debug_type($value) . ")";
            }
        };

        self::assertSame("=== (test-null) || === (test-string) || === (test-int) || === (test-float) || === (test-array) || === (test-bool)", (new IsIdenticalToAnyOf(...$expected))->describe($serialiser));
        self::assertEmpty($expected);
    }
}
