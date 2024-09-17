<?php

declare(strict_types=1);

namespace Mokkd\Expectations;

use Mokkd\Contracts\Expectation as ExpectationContract;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

class Expectation extends AbstractExpectation implements ExpectationContract
{
    /** @var MatcherContract[] $argumentMatchers  */
    private array $argumentMatchers;

    /** @param MatcherContract[] $expectedArgs */
    public function __construct(mixed ...$expectedArgs)
    {
        $this->argumentMatchers = $expectedArgs;
    }

    public static function any(): Any
    {
        return new Any();
    }

    public function matches(...$args): bool
    {
        if (count($args) !== count($this->argumentMatchers)) {
            return false;
        }

        for ($idx = 0; $idx < count($args); $idx++) {
            if (!$this->argumentMatchers[$idx]->matches($args[$idx])) {
                return false;
            }
        }

        return true;
    }

    public function isSatisfied(): bool
    {
        return $this->expectedCount === ExpectationContract::UnlimitedTimes || $this->matchCount === $this->expectedCount;
    }

    public function message(SerialiserContract $serialiser): string
    {
        $arguments = array_map(static fn(MatcherContract $matcher): string => $matcher->describe($serialiser), $this->argumentMatchers);
        return "(" . implode(", ", $arguments) . ") expected to be called exactly {$this->expectedCount} time(s) but called {$this->matchCount} time(s)";
    }
}

