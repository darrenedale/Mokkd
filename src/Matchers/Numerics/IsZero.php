<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Numerics;

/** Matcher that requires a numeric value of any type that is 0. */
class IsZero extends IsEqualTo
{
    public function __construct()
    {
        parent::__construct(0);
    }
}
