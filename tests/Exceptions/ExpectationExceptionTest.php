<?php

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
