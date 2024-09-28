<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use ArrayAccess;

/** Matcher that requires any IteratorAggregate. */
class IsArrayAccess extends IsInstanceOf
{
    public function __construct()
    {
        parent::__construct(ArrayAccess::class);
    }
}
