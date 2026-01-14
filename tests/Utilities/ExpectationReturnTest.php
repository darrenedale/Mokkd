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

namespace MokkdTests\Utilities;

use LogicException;
use Mokkd\Utilities\ExpectationReturn;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use StdClass;

#[CoversClass(ExpectationReturn::class)]
class ExpectationReturnTest extends TestCase
{
    /** Ensure void() creates a void return expectation. */
    public function testVoid1(): void
    {
        self::assertTrue(ExpectationReturn::void()->isVoid());
    }

    public static function dataForTestCreate1(): iterable
    {
        yield "zero" => [0];
        yield "int" => [42];
        yield "float" => [3.1415927];
        yield "empty-string" => [""];
        yield "whitespace-string" => ["  "];
        yield "string" => ["Mokkd"];
        yield "empty-array" => [[]];
        yield "array" => [[1, "two", 3.14]];
        yield "object" => [new StdClass()];
        yield "resource" => [fopen("php://memory", "r")];
        yield "null" => [null];
    }

    /** Ensure create() creates a return expectation with the correct value. */
    #[DataProvider("dataForTestCreate1")]
    public function testCreate1(mixed $value): void
    {
        $actual = ExpectationReturn::create($value);
        self::assertFalse($actual->isVoid());
        self::assertSame($value, $actual->value());
    }

    /** Ensure value() throws the correct LogicException when invoked on a void ExpectationReturn. */
    public function testValue1(): void
    {
        $actual = ExpectationReturn::void();
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("value() called on void return");
        $actual->value();
    }
}
