<?php

declare(strict_types=1);

namespace MokkdTests\Expectations;

use Mokkd\Expectations\Any;
use Mokkd\Expectations\Expectation;
use MokkdTests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Expectation::class)]
class ExpectationTest extends TestCase
{
    /** Ensure any() returns an Any matcher. */
    public function testAny1(): void
    {
        self::assertInstanceOf(Any::class, Expectation::any());
    }

    /** Ensure matches returns true for no args. */

    /** Ensure matches returns false for on arg count mismatch. */

    /** Ensure matches calls matches() for all matchers up to the first that fails. */

    /** Ensure isSatisfied() works with unlimited call counts. */

    /** Ensure isSatisfied() correctly verifies the call count. */

    /** Ensure message() produces the expected error message. */
}
