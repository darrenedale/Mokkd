<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Floats;

/**
 * Matches a float that equals 0.
 */
class IsZero extends IsEqualTo
{
    public function __construct()
    {
        parent::__construct(0.0);
    }
}
