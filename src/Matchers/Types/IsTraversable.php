<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Traversable;

/** Matcher that requires any IteratorAggregate. */
class IsTraversable extends IsInstanceOf
{
    public function __construct()
    {
        parent::__construct(Traversable::class);
    }
}
