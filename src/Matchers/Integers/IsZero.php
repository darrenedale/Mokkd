<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Integers;

/**
 * Matches an int equal to 0.
 */
class IsZero extends IsEqualTo {
    public function __construct()
    {
        parent::__construct(0);
    }
}
