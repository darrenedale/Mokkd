<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use IteratorAggregate;

/** Matcher that requires any IteratorAggregate. */
class IsIteratorAggregate extends IsInstanceOf
{
    public function __construct()
    {
        parent::__construct(IteratorAggregate::class);
    }
}
