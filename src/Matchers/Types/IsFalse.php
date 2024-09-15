<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Matchers\Comparisons\IsIdenticalTo;

/** Matcher that requires a true value. */
class IsFalse extends IsIdenticalTo
{
    public function __construct()
    {
        parent::__construct(false);
    }
}
