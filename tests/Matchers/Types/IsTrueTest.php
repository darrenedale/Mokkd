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

use Mokkd\Matchers\Types\IsTrue;
use MokkdTests\CreatesMockSerialiser;
use MokkdTests\CreatesNullSerialiser;
use MokkdTests\Matchers\DataFactory;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IsTrueTest extends TestCase
{
    use CreatesMockSerialiser;

    /** Ensure true successfully matches. */
    public function testMatches1(): void
    {
        self::assertTrue((new IsTrue())->matches(true));
    }

    public static function dataForTestMatches2(): iterable
    {
        yield "null" => [null];
        yield from DataFactory::strings();
        yield from DataFactory::integers();
        yield from DataFactory::floats();
        yield from DataFactory::booleanFalse();
        yield from DataFactory::objects();
        yield from DataFactory::resources();
    }

    /** Ensure values other than true fail to match. */
    #[DataProvider("dataForTestMatches2")]
    public function testMatches2(mixed $test): void
    {
        self::assertFalse((new IsTrue())->matches($test));
    }

    /** Ensure the IsTrue matcher describes itself as expected. */
    public function testDescribe1(): void
    {
        self::assertSame("(bool) true", (new IsTrue())->describe(self::mockSerialiser(true, "(bool) true")));
    }
}
