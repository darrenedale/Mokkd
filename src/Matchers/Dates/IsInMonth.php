<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Dates;

use DateTimeInterface;
use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;
use Mokkd\Month;

/**
 * Matcher that requires a date in a given month, and optionally a given year.
 */
class IsInMonth implements MatcherContract
{
    private int $month;

    private ?int $year;

    /**
     * @param int|Month $month The month the date must be in. Months are numbered from 1-12.
     * @param int|null $year The year the date must be in, or null if it can be in any year.
     */
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
            && ((int) $actual->format("m")) === $this->month
            && (null === $this->year || ((int) $actual->format("Y")) === $this->year);
    }

    public function describe(Serialiser $serialiser): string
    {
        if (null === $this->year) {
            return sprintf("(DateTimeInterface) {month == %s}", $this->month->name);
        } else {
            return sprintf("(DateTimeInterface) {month == %s && year == %04d}", $this->month->name, $this->year);
        }
    }
}
