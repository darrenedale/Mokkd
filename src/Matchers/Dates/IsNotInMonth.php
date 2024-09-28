<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Dates;

use DateTimeInterface;
use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;
use Mokkd\Month;

/**
 * Matcher that requires a date in not a given month, or a given month and year.
 *
 * If a year is specified, the tested date must not be in the provided month in the provided year; otherwise, it must
 * not be in the provided month in any year.
 */
class IsNotInMonth implements MatcherContract
{
    private int $month;

    private ?int $year;

    public function __construct(int|Month $month, ?int $year = null)
    {
        if ($month instanceof Month) {
            $month = $month->value;
        } else {
            assert(0 < $month && 13 > $month, new LogicException("Expected valid month number, found {$month}"));
        }

        $this->month = $month;
        $this->year = $year;
    }

    public function matches(mixed $actual): bool
    {
        return ($actual instanceof DateTimeInterface)
            && (
                // matches if it's not in the given month
                ((int) $actual->format("m")) !== $this->month
                // or if it is in the given month, there's a year set and it's not in the same year
                || (null !== $this->year && ((int) $actual->format("Y")) !== $this->year)
            );
    }

    public function describe(Serialiser $serialiser): string
    {
        if (null === $this->year) {
            return sprintf("(DateTimeInterface) {month != %s}", $this->month->name);
        } else {
            return sprintf("(DateTimeInterface) {month != %s || year == %04d}", $this->month->name, $this->year);
        }
    }
}
