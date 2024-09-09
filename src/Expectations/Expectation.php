<?php
declare(strict_types=1);

namespace Mokkd\Expectations;

use Mokkd\Contracts\Expectation as ExpectationContract;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Core;

class Expectation extends AbstractExpectation implements ExpectationContract
{
    /** @var MatcherContract[] $expectedArgs  */
    private array $expectedArgs;

    /** @param MatcherContract[] $expectedArgs */
    public function __construct(mixed ...$expectedArgs)
    {
        $this->expectedArgs = $expectedArgs;
    }

    public static function any(): Any
    {
        return new Any();
    }

    public function matches(...$args): bool
    {
        if (count($args) !== count($this->expectedArgs)) {
            return false;
        }

        for ($idx = 0; $idx < count($args); $idx++) {
            if (!$this->expectedArgs[$idx]->matches($args[$idx])) {
                return false;
            }
        }

        return true;
    }

    public function isSatisfied(): bool
    {
        return $this->expectedCount === ExpectationContract::UnlimitedTimes || $this->matchCount === $this->expectedCount;
    }

    public function message(): string
    {
        return "(" . implode(", ", Core::serialiser()->serialise(...$this->expectedArgs)) . ") expected to be called exactly {$this->expectedCount} time(s) but called {$this->matchCount} time(s)";
    }
}

