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
use Mokkd\Expectations\Expectation;
use MokkdTests\TestCase;
use MokkdTests\TestException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ExpectationException::class)]
class ExpectationExceptionTest extends TestCase
{
    private const TestExceptionMessage = "A test error occurred with this expectation";


    /** Ensure we can set (and get) the expectation. */
    public function testGetConstructor1(): void
    {
        $expectation = new Expectation();
        self::assertSame($expectation, (new ExpectationException($expectation))->getExpectation());
    }


    /** Ensure we can set the message. */
    public function testGetConstructor2(): void
    {
        self::assertSame(self::TestExceptionMessage, (new ExpectationException(new Expectation(), self::TestExceptionMessage))->getMessage());
    }


    /** Ensure we can set a previous exception. */
    public function testGetConstructor3(): void
    {
        $exception = new TestException();
        self::assertSame($exception, (new ExpectationException(new Expectation(), previous: $exception))->getPrevious());
    }
}
