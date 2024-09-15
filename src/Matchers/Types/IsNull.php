<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Matchers\Comparisons\IsIdenticalTo;

/** Matcher that requires a null value. */
class IsNull extends IsIdenticalTo
{
    public function __construct()
    {
        parent::__construct(null);
    }
}
