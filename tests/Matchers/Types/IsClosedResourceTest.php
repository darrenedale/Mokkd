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

use Mokkd\Matchers\Types\IsClosedResource;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsClosedResourceTest extends TestCase
{
    use CreatesNullSerialiser;

    public static function dataForTestMatches1(): iterable
    {
        yield from DataFactory::closedResources();
    }

    /** Ensure all closed resources (or a reasonable approximation of the set of resources) successfully match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(mixed $test): void
    {
        self::assertTrue((new IsClosedResource())->matches($test));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::booleans();
        yield from DataFactory::integers();
        yield from DataFactory::floats();
        yield from DataFactory::strings();
        yield from DataFactory::arrays();
        yield from DataFactory::objects();
        yield from DataFactory::openResources();
    }

    /** Ensure non-resources and open resources fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $test): void
    {
        self::assertFalse((new IsClosedResource())->matches($test));
    }

    /** Ensure the IsClosedResource matcher describes itself as expected. */
    public function testDescribe1(): void
    {
        self::assertSame("(resource) {closed}", (new IsClosedResource())->describe(self::nullSerialiser()));
    }
}
