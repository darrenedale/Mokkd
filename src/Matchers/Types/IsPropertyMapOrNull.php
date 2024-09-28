<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Composite\MatchesAnyOf;

/**
 * Matcher that requires the test value to be null or any associative array whose keys are all strings.
 *
 * Note that an empty array qualifies as a property map.
 */
class IsPropertyMapOrNull extends MatchesAnyOf
{
    public function __construct()
    {
        parent::__construct(new IsNull(), new IsPropertyMap());
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?array) {property-map}";
    }
}
