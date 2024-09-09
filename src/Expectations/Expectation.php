<?php
declare(strict_types=1);

namespace Mokkd\Expectations;

use Mokkd\Contracts\Matcher as MatcherContract;

class Expectation extends AbstractExpectation
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
}
