<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Generator;

/** Matcher that requires any Generator. */
class IsGenerator extends IsInstanceOf
{
    public function __construct()
    {
        parent::__construct(Generator::class);
    }
}
