<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Times;

use DateTimeInterface;
use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/**
 * The bounds are inclusive.
 *
 * Only the time portion of the DateTime is significant - the date part is discarded. So a DateTime representing
 * 2024-04-23 16:00:00 will be considered between 2000-07-14 14:00:00 and 1996-08-01 18:00:00 because 16:00:00 is
 * between 14:00:00 and 18:00:00.
 *
 * Sub-second time components are discarded.
 */
class IsBetween implements MatcherContract
{
    private const DisplayFormat = "H:i:s";

    private const ComparisonFormat = "His";

    private int $lowerBoundNumeric;

    private int $upperBoundNumeric;

    private DateTimeInterface $lowerBound;

    private DateTimeInterface $upperBound;

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
        return $actual >= $this->lowerBoundNumeric && $actual <= $this->upperBoundNumeric;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A date between {$this->lowerBound->format(self::DisplayFormat)} and {$this->upperBound->format(self::DisplayFormat)} inclusive";
    }
}
