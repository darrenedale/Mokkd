<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Iterator;

/**
 * Matcher that requires the test value to be any object implementing Iterator.
 */
class IsIterator extends IsInstanceOf
{
    public function __construct()
    {
        parent::__construct(Iterator::class);
    }
}
