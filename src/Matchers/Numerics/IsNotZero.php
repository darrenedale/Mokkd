<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Numerics;

/**
 * Matches any numeric value of any type that is not 0.
 */
class IsNotZero extends IsNumericNotEqualTo
{
    public function __construct()
    {
        parent::__construct(0);
    }
}
