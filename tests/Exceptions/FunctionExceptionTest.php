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

namespace MokkdTests\Exceptions;

use Mokkd\Exceptions\ExpectationException;
use Mokkd\Exceptions\FunctionException;
use Mokkd\Expectations\Expectation;
use MokkdTests\TestCase;
use MokkdTests\TestException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FunctionException::class)]
class FunctionExceptionTest extends TestCase
{
    private const TestExceptionMessage = "A test error occurred with this function";

    private const TestFunctionName = "mokkd_test_function";


    /** Ensure we can set (and get) the function name. */
    public function testGetConstructor1(): void
    {
        self::assertSame(self::TestFunctionName, (new FunctionException(self::TestFunctionName))->getFunctionName());
    }


    /** Ensure we can set the message. */
    public function testGetConstructor2(): void
    {
        self::assertSame(self::TestExceptionMessage, (new FunctionException(self::TestFunctionName, self::TestExceptionMessage))->getMessage());
    }


    /** Ensure we can set a previous exception. */
    public function testGetConstructor3(): void
    {
        $exception = new TestException();
        self::assertSame($exception, (new FunctionException(self::TestFunctionName, previous: $exception))->getPrevious());
    }
}
