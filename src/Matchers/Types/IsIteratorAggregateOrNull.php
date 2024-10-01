<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Composite\MatchesAnyOf;

/**
 * Matcher that requires the test value to be any Iterator or null.
 */
class IsIteratorAggregateOrNull extends MatchesAnyOf
{
    public function __construct()
    {
        parent::__construct(new IsNull(), New IsIteratorAggregate());
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?IteratorAggregate) {any}";
    }
}
