<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Matchers\Comparisons\IsIdenticalTo;

/**
 * Matcher that requires the test value to be true.
 */
class IsTrue extends IsIdenticalTo
{
    public function __construct()
    {
        parent::__construct(true);
    }
}
