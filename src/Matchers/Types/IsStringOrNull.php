<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Composite\MatchesAnyOf;

/**
 * Matcher that requires the test value to be any string or null.
 */
class IsStringOrNull extends MatchesAnyOf
{
    public function __construct()
    {
        parent::__construct(new IsNull(), new IsString());
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?string) {any}";
    }
}
