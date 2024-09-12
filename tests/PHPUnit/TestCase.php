<?php

declare(strict_types=1);

namespace MokkdTest\PhpUnit;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

use function uopz_get_return;
use function uopz_set_return;
use function uopz_unset_return;

class TestCase extends PHPUnitTestCase
{
    /**
     * Call this if the test is externally verified (e.g. by Mockery).
     *
     * This prevents PHPUnit from marking the test as risky on the basis that it doesn't perform any assertions.
     */
    protected static function markTestAsExternallyVerified(): void
    {
        self::assertTrue(true);
    }
}
