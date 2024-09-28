<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Dates;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use LogicException;
use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser;

/**
 * Matcher that requires a date in UTC.
 */
class IsInUtc implements MatcherContract
{
    public function matches(mixed $actual): bool
    {
        return ($actual instanceof DateTimeInterface) && $actual->getTimezone()->getName() === "UTC";
    }

    public function describe(Serialiser $serialiser): string
    {
        return "(DateTimeInterface) {tz == UTC}";
    }
}
