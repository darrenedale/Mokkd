<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Times;

use DateTimeInterface;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/**
 * The bound is exclusive.
 *
 * Only the time portion of the DateTime is significant - the date part is discarded. So a DateTime representing
 * 2020-04-23 16:00:00 will be considered after 2024-07-14 14:00:00 because 16:00:00 is after 14:00:00.
 *
 * Sub-second time components are discarded.
 */
class IsAfter implements MatcherContract
{
    private const DisplayFormat = "H:i:s";

    private const ComparisonFormat = "His";

    private int $lowerBoundNumeric;

    private DateTimeInterface $lowerBound;

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

        return ((int) $actual->format(self::ComparisonFormat)) > $this->lowerBoundNumeric;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A time after {$this->lowerBound->format(self::DisplayFormat)}";
    }
}
