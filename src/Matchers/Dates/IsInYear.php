<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Dates;

use DateTimeInterface;
use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/**
 * Matcher that requires a date in a given year.
 */
class IsInYear implements MatcherContract
{
    private int $year;

    public function __construct(int $year)
    {
        $this->year = $year;
    }

    public function matches(mixed $actual): bool
    {
        return ($actual instanceof DateTimeInterface) && ((int) $actual->format("Y")) === $this->year;
    }

    public function describe(Serialiser $serialiser): string
    {
        return sprintf("(DateTimeInterface) {year == %04d}", $this->year);
    }
}
