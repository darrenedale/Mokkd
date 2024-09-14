<?php

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
