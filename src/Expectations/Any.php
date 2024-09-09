<?php

declare(strict_types=1);

namespace Mokkd\Expectations;

use Mokkd\Contracts\Expectation as ExpectationContract;

class Any extends AbstractExpectation
{
    public function matches(...$args): bool
    {
        return true;
    }

    public function isSatisfied(): bool
    {
        return $this->expectedCount === ExpectationContract::UnlimitedTimes || $this->matchCount === $this->expectedCount;
    }

    public function notSatisfiedMessage(): string
    {
        return "({any arguments}) expected to be called exactly {$this->expectedCount} time(s) but called {$this->matchCount} time(s)";
    }
}
