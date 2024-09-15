<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Matchers\Identity;

/** Matcher that requires a true value. */
class IsFalse extends Identity
{
    public function __construct()
    {
        parent::__construct(false);
    }
}
