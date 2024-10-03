<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Numerics;

/** Matcher that requires a numeric value of any type that is not 0. */
class IsNotZero extends IsNotEqualTo
{
    public function __construct()
    {
        parent::__construct(0);
    }
}
