<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Composite\MatchesAnyOf;

/**
 * Matcher that requires the test value to be an associative array or null.
 *
 * An associative array has keys that are:
 * - strings; or
 * - a mixture of strings and ints; or
 * - non-consecutive ints; or
 * - consecutive ints that don't begin at 0
 *
 * Note that an empty array qualifies as an associative array.
 */
class IsAssociativeArrayOrNull extends MatchesAnyOf
{
    public function __construct()
    {
        parent::__construct(new IsNull(), new IsArray());
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?array) {associative}";
    }
}
