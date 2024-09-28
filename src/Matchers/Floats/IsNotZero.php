<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Floats;

/**
 * Matches any float that is not 0.
 */
class IsNotZero extends IsFloatNotEqualTo
{
    public function __construct()
    {
        parent::__construct(0.0);
    }
}
