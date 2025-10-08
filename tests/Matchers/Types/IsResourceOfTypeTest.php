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

use Mokkd\Matchers\Types\IsResourceOfType;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsResourceOfTypeTest extends TestCase
{
    use CreatesNullSerialiser;

    private const Types = [
        ["stream"],
        ["Unknown"],
        [""],
    ];

    public static function dataForTestMatches1(): iterable
    {
        $type = ["stream" => ["stream"]];

        yield from DataFactory::matrix($type, DataFactory::dataStream());
        yield from DataFactory::matrix($type, DataFactory::memoryStream());
        yield from DataFactory::matrix($type, DataFactory::temporaryStream());
    }

    /** Ensure all resources (or a reasonable approximation of the set of resources) successfully match. */
    #[DataProvider("dataForTestMatches1")]
    public function testMatches1(string $type, mixed $test): void
    {
        self::assertTrue((new IsResourceOfType($type))->matches($test));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield from DataFactory::matrix(self::Types, ["null" => [null]]);
        yield from DataFactory::matrix(self::Types, DataFactory::booleans());
        yield from DataFactory::matrix(self::Types, DataFactory::integers());
        yield from DataFactory::matrix(self::Types, DataFactory::floats());
        yield from DataFactory::matrix(self::Types, DataFactory::strings());
        yield from DataFactory::matrix(self::Types, DataFactory::arrays());
        yield from DataFactory::matrix(self::Types, DataFactory::objects());
    }

    /** Ensure non-resources fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(string $type, mixed $test): void
    {
        self::assertFalse((new IsResourceOfType($type))->matches($test));
    }

    public static function dataForTestType1(): iterable
    {
        return self::Types;
    }

    /** Ensure the type provided to the constructor can be retrieved. */
    #[DataProvider("dataForTestType1")]
    public function testType1(string $type): void
    {
        self::assertSame($type, (new IsResourceOfType($type))->type());
    }

    public static function dataForTestDescribe1(): iterable
    {
        return self::Types;
    }

    /** Ensure the IsResourceOfType matcher describes itself as expected. */
    #[DataProvider("dataForTestDescribe1")]
    public function testDescribe1(string $type): void
    {
        self::assertSame("(resource) {{$type}}", (new IsResourceOfType($type))->describe(self::nullSerialiser()));
    }
}
