<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Dates;

use DateTimeInterface;
use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/** The bounds are inclusive. */
class IsDateBetween implements MatcherContract
{
    private const DisplayFormat = "Y-m-d H:i:s T";

    private int $lowerBoundNumeric;

    private int $upperBoundNumeric;

    private DateTimeInterface $lowerBound;

    private DateTimeInterface $upperBound;

    public function __construct(DateTimeInterface $lowerBound, DateTimeInterface $upperBound)
    {
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
        $this->lowerBoundNumeric = (int) $lowerBound->format("Ymd");
        $this->upperBoundNumeric = (int) $upperBound->format("Ymd");
        assert($this->lowerBoundNumeric <= $this->upperBoundNumeric, new LogicException("Expected upper bound >= lower bound, found {$lowerBound->format(self::DisplayFormat)} and {$upperBound->format(self::DisplayFormat)}"));
    }

    public function matches(mixed $actual): bool
    {
        if (!$actual instanceof DateTimeInterface) {
            return false;
        }

        $actual = (int) $actual->format("Ymd");
        return $actual >= $this->lowerBoundNumeric && $actual <= $this->upperBoundNumeric;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A date between {$this->lowerBound->format("Y-m-d")} and {$this->upperBound->format("Y-m-d")} inclusive";
    }
}
