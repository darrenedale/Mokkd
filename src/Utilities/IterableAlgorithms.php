<?php

declare(strict_types=1);

namespace Mokkd\Utilities;

class IterableAlgorithms
{
    /**
     * Check that all values in an iterable satisfy a predicate.
     *
     * @return true if the predicate returns true for all values, false otherwise.
     */
    public static function all(iterable $values, callable $predicate): bool
    {
        foreach ($values as $value) {
            if (!$predicate($value)) {
                return false;
            }
        }

        return true;
    }

    public static function allKeys(iterable $values, callable $predicate): bool
    {
        foreach ($values as $key => $value) {
            if (!$predicate($key)) {
                return false;
            }
        }

        return true;
    }
}
