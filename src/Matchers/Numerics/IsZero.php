<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Numerics;

/**
 * Match a numeric value of any type that is 0.
 */
class IsZero extends IsNumericEqualTo
{
    public function __construct()
    {
        parent::__construct(0);
    }
}
