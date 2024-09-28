<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Closure;

/**
 * Matcher that requires the test value to be any Closure.
 */
class IsClosure extends IsInstanceOf
{
    public function __construct()
    {
        parent::__construct(Closure::class);
    }
}
