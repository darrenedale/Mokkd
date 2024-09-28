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
 * 2024-04-23 14:00:00 will be considered before 2020-07-14 16:00:00 because 14:00:00 is before 16:00:00.
 *
 * Sub-second time components are discarded.
 */
class IsBefore implements MatcherContract
{
    private const DisplayFormat = "H:i:s";

    private const ComparisonFormat = "His";

    private int $upperBoundNumeric;

    private DateTimeInterface $upperBound;

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

        return ((int) $actual->format(self::ComparisonFormat)) < $this->upperBoundNumeric;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A time before {$this->upperBound->format(self::DisplayFormat)}";
    }
}
