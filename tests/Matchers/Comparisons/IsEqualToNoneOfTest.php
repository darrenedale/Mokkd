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

namespace MokkdTests\Matchers\Comparisons;

use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Comparisons\IsEqualToNoneOf;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(IsEqualToNoneOf::class)]
class IsEqualToNoneOfTest extends TestCase
{
    public const Constraints = [null, "", 42, 3.1415927, [], true];

    // These values are guaranteed not to match any of the data yielded by unequalValues()
    public const NotEqualConstraints = ["function", [99, 44, "mokkd"], 21, 1.557, "null"];

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
        foreach (DataFactory::equalValues() as $label => $args) {
            # ensure we have datasets where the matching arg is anywhere within the constraint set
            yield $label => [$args[0], ...self::embedEqualConstraint($args[1])];
        }

        foreach (DataFactory::identicalValues() as $label => $args) {
            yield $label => [$args[0], ...self::embedEqualConstraint($args[1])];
        }
    }

    /** Ensure values that are equal to one of the constraint set don't match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(mixed $value, mixed ...$constraintSet): void
    {
        self::assertFalse((new IsEqualToNoneOf(...$constraintSet))->matches($value));
    }

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

    /** Ensure values that are not equal to any of the constraint set match successfully. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $value, mixed ...$constraintSet): void
    {
        self::assertTrue((new IsEqualToNoneOf(...$constraintSet))->matches($value));
    }

    /** Ensure true does not match unexpected values of other types. */
    public function testMatches3(): void
    {
        self::assertTrue((new IsEqualToNoneOf(0, 0.0, false, null, ""))->matches(true));
    }

    /** Ensure false does not match unexpected values of other types. */
    public function testMatches4(): void
    {
        self::assertTrue((new IsEqualToNoneOf(1, 0.0000001, true, "false"))->matches(false));
    }

    /** Ensure identical objects don't match. */
    public function testMatches5(): void
    {
        $object = new class {};
        self::assertFalse((new IsEqualToNoneOf(...self::embedEqualConstraint($object)))->matches($object));
    }

    /** Ensure equal objects match don't match. */
    public function testMatches6(): void
    {
        $object = new class {};
        self::assertFalse((new IsEqualToNoneOf(...self::embedEqualConstraint($object)))->matches(clone $object));
    }

    /** Ensure resources match like values. */
    public function testMatches7(): void
    {
        $resource = fopen("php://memory", "r");
        $equalResource = $resource;
        self::assertFalse((new IsEqualToNoneOf(...self::embedEqualConstraint($resource)))->matches($equalResource));
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
                IsEqualToNoneOfTest::assertNotEmpty($this->expected);
                IsEqualToNoneOfTest::assertEquals(array_shift($this->expected), $value);

                return  "(test-" . get_debug_type($value) . ")";
            }
        };

        self::assertSame("!= (test-null) && != (test-string) && != (test-int) && != (test-float) && != (test-array) && != (test-bool)", (new IsEqualToNoneOf(...$expected))->describe($serialiser));
        self::assertEmpty($expected);
    }
}
