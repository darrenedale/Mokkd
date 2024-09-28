<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Dates;

use DateTimeInterface;
use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/**
 * Matcher that requires a date within two bounding dates.
 *
 * The bounds are exclusive. Only the date portion of the DateTime is significant - the time part is discarded.
 */
class IsWithin implements MatcherContract
{
    private const DisplayFormat = "Y-m-d";

    private const ComparisonFormat = "Ymd";

    private int $lowerBoundNumeric;

    private int $upperBoundNumeric;

    private DateTimeInterface $lowerBound;

    private DateTimeInterface $upperBound;

    /**
     * @param DateTimeInterface $lowerBound The date the value must be after.
     * @param DateTimeInterface $upperBound The date the value must be before.
     *
     * @throws LogicException if the date part of $upperBound is before the date part of $lowerBound.
     */
    public function __construct(DateTimeInterface $lowerBound, DateTimeInterface $upperBound)
    {
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
        $this->lowerBoundNumeric = (int) $lowerBound->format(self::ComparisonFormat);
        $this->upperBoundNumeric = (int) $upperBound->format(self::ComparisonFormat);
        assert($this->lowerBoundNumeric <= $this->upperBoundNumeric, new LogicException("Expected upper bound >= lower bound, found {$lowerBound->format(self::DisplayFormat)} and {$upperBound->format(self::DisplayFormat)}"));
    }

    public function matches(mixed $actual): bool
    {
        if (!$actual instanceof DateTimeInterface) {
            return false;
        }

        $actual = (int) $actual->format(self::ComparisonFormat);
        return $actual > $this->lowerBoundNumeric && $actual < $this->upperBoundNumeric;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "(DateTimeInterface) > {$this->lowerBound->format(self::DisplayFormat)} && < {$this->upperBound->format(self::DisplayFormat)}";
    }
}
