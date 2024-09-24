<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Numeric;

use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;
use Mokkd\Matchers\Comparisons\IsEqualTo;

/**
 * Match a numeric value of any type that are not 0.
 */
class IsNotZero extends IsNumericNotEqualTo
{
    public function __construct()
    {
        parent::__construct(0);
    }
}
