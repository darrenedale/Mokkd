<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Dates;

use DateTimeInterface;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/**
 * Matcher that requires a date on or after a bounding date
 *
 * The bound is exclusive. Only the date portion of the DateTime is significant - the time part is discarded.
 */
class IsOnOrAfter implements MatcherContract
{
    private const DisplayFormat = "Y-m-d";

    private const ComparisonFormat = "Ymd";

    private int $lowerBoundNumeric;

    private DateTimeInterface $lowerBound;

    /**
     * @param DateTimeInterface $lowerBound The date the value must be on or after.
     */
    public function __construct(DateTimeInterface $lowerBound)
    {
        $this->lowerBound = $lowerBound;
        $this->lowerBoundNumeric = (int) $lowerBound->format(self::ComparisonFormat);
    }

    public function matches(mixed $actual): bool
    {
        if (!$actual instanceof DateTimeInterface) {
            return false;
        }

        return ((int) $actual->format(self::ComparisonFormat)) >= $this->lowerBoundNumeric;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "(DateTimeInterface) >= {$this->lowerBound->format(self::DisplayFormat)}";
    }
}
