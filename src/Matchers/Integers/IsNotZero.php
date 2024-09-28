<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Integers;

/**
 * Matches any int that is not 0.
 */
class IsNotZero extends IsNotEqualTo
{
    public function __construct()
    {
        parent::__construct(0);
    }
}
