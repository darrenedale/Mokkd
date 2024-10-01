<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Throwable;

/**
 * Matcher that requires the test value to be any Throwable.
 */
class IsThrowable extends IsInstanceOf
{
    public function __construct()
    {
        parent::__construct(Throwable::class);
    }
}
