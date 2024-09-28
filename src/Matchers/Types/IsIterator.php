<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Iterator;

/** Matcher that requires any Iterator. */
class IsIterator extends IsInstanceOf
{
    public function __construct()
    {
        parent::__construct(Iterator::class);
    }
}
