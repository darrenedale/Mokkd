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
 * Matcher that requires a date in a given year.
 */
class IsInTimeZoneWithSameOffsetAs implements MatcherContract
{
    private int $offset;

    private string $name;

    private static DateTimeZone $epoch;

    public function __construct(string|DateTimeZone $tz)
    {
        if (!isset(self::$epoch)) {
            self::$epoch = new DateTimeZone("UTC");
        }

        if (is_string($tz)) {
            try {
                $tz = new DateTimeZone($tz);
            } catch (Exception $err) {
                throw new LogicException("Expected valid timezone, found {$tz}", previous: $err);
            }
        }

        $this->name = $tz->getName();
        $this->offset = self::$epoch->getOffset(new DateTimeImmutable("@0", $tz));
    }

    public function matches(mixed $actual): bool
    {
        return ($actual instanceof DateTimeInterface) && self::$epoch->getOffset($actual) === $this->offset;
    }

    public function describe(Serialiser $serialiser): string
    {
        return "A date with the same UTC offset as {$this->name}";
    }
}
