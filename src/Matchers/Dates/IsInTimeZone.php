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
 * Matcher that requires a date in a given timezone.
 *
 * The value's timezone name must match the given timezone - different names for equivalent timezones do not match.
 */
class IsInTimeZone implements MatcherContract
{
    private string $name;

    /**
     * @param string|DateTimeZone $tz The timezone the date must be in.
     */
    public function __construct(string|DateTimeZone $tz)
    {
        if (is_string($tz)) {
            try {
                $tz = new DateTimeZone($tz);
            } catch (Exception $err) {
                throw new LogicException("Expected valid timezone, found {$tz}", previous: $err);
            }
        }

        $this->name = $tz->getName();
    }

    public function matches(mixed $actual): bool
    {
        return ($actual instanceof DateTimeInterface) && $actual->getTimezone()->getName() === $this->name;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "(DateTimeInterface) {tz == {$this->name}}";
    }
}
