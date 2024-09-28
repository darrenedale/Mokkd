<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Dates;

use DateTimeInterface;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/**
 * Matcher that requires a date on or before a bounding date
 *
 * Only the date portion of the DateTime is significant - the time part is discarded.
 */
class IsOnOrBefore implements MatcherContract
{
    private const DisplayFormat = "Y-m-d";

    private const ComparisonFormat = "Ymd";

    private int $upperBoundNumeric;

    private DateTimeInterface $upperBound;

    /**
     * @param DateTimeInterface $upperBound The date the value must be on or before.
     */
    public function __construct(DateTimeInterface $upperBound)
    {
        $this->upperBound = $upperBound;
        $this->upperBoundNumeric = (int) $upperBound->format(self::ComparisonFormat);
    }

    public function matches(mixed $actual): bool
    {
        if (!$actual instanceof DateTimeInterface) {
            return false;
        }

        return ((int) $actual->format(self::ComparisonFormat)) <= $this->upperBoundNumeric;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "(DateTimeInterface) <= {$this->upperBound->format(self::DisplayFormat)}";
    }
}
