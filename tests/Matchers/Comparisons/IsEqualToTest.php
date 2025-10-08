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
use Mokkd\Matchers\Comparisons\IsEqualTo;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(IsEqualTo::class)]
class IsEqualToTest extends TestCase
{
    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::identicalValues();
        yield from DataFactory::equalValues();
    }

    /** Ensure values that are equal match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(mixed $matchAgainst, mixed $value): void
    {
        self::assertTrue((new IsEqualTo($matchAgainst))->matches($value));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield from DataFactory::unequalValues();
    }

    /** Ensure values that are not equal don't match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $matchAgainst, mixed $value): void
    {
        self::assertFalse((new IsEqualTo($matchAgainst))->matches($value));
    }

    /** Ensure identical objects match. */
    public function testMatches3(): void
    {
        $object = new class {};
        self::assertTrue((new IsEqualTo($object))->matches($object));
    }

    /** Ensure equal objects match. */
    public function testMatches4(): void
    {
        $object = new class {};
        self::assertTrue((new IsEqualTo($object))->matches(clone $object));
    }

    /** Ensure resources match like values. */
    public function testMatches5(): void
    {
        $resource = fopen("php://memory", "r");
        $equalResource = $resource;
        self::assertTrue((new IsEqualTo($resource))->matches($equalResource));
    }

    /** Ensure the serialiser is used to describe the matcher. */
    public function testDescribe1(): void
    {
        $serialiser = new class implements SerialiserContract
        {
            public function serialise(mixed $value): string
            {
                IsEqualToTest::assertSame("test value", $value);
                return "(test-string) \"test value\"";
            }
        };

        self::assertSame("== (test-string) \"test value\"", (new IsEqualTo("test value"))->describe($serialiser));
    }
}
