<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Composite;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Utilities\IterableAlgorithms;

/**
 * Composite matcher that requires a value to match at least one of its component matchers.
 */
class MatchesAnyOf implements MatcherContract
{
    /** @var MatcherContract[] */
    private array $matchers;

    /**
     * @param MatcherContract $matcher The first matcher that the value may match.
     * @param MatcherContract ...$matchers The other matchers that the value may match.
     */
    public function __construct(MatcherContract $matcher, MatcherContract ...$matchers)
    {
        $this->matchers = [$matcher, ...$matchers];
    }

    public function matches(mixed $actual): bool
    {
        return IterableAlgorithms::any($this->matchers, static fn (MatcherContract $matcher) => $matcher->matches($actual));
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(" . implode(
            ") || (",
            iterator_to_array(IterableAlgorithms::transform($this->matchers, static fn (MatcherContract $matcher) => $matcher->describe($serialiser)))
        ) . ")";
    }
}
