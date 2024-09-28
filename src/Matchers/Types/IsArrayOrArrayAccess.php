<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Matchers\Composite\MatchesAnyOf;

/**
 * Matcher that requires the test value to be any array or any object implementing ArrayAccess.
 */
class IsArrayOrArrayAccess extends MatchesAnyOf
{
    public function __construct()
    {
        parent::__construct(new IsArray(), new IsArrayAccess());
    }
}
