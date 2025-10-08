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

namespace MokkdTests\Matchers\Types;

use Mokkd\Matchers\Types\IsPropertyMap;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsPropertyMapTest extends TestCase
{
    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::propertyMaps();
    }

    /** Ensure all property-maps (or a reasonable approximation thereof) successfully match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(array $test): void
    {
        self::assertTrue((new IsPropertyMap())->matches($test));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::nonEmptyAssociativeArrays();
        yield from DataFactory::nonEmptyListArrays();
        yield from DataFactory::strings();
        yield from DataFactory::integers();
        yield from DataFactory::floats();
        yield from DataFactory::booleans();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
    }

    /** Ensure non-property maps fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $test): void
    {
        self::assertFalse((new IsPropertyMap())->matches($test));
    }

    /** Ensure the IsPropertyMap matcher describes itself as expected. */
    public function testDescribe1(): void
    {
        self::assertSame("(array) {property-map}", (new IsPropertyMap())->describe(self::nullSerialiser()));
    }
}
