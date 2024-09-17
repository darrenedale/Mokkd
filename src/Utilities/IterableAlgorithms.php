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

    /**
     * Check that all keys in an iterable satisfy a predicate.
     *
     * @return true if the predicate returns true for all keys, false otherwise.
     */
    public static function allKeys(iterable $values, callable $predicate): bool
    {
        foreach ($values as $key => $value) {
            if (!$predicate($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @template T
     *
     * Transform the values of an iterable using a mutator function.
     *
     * Iterable keys are preserved.
     *
     * @param iterable<int|string,T> $values The values to transform.
     * @param callable $transform The callable to use to transform each value. Required to accept a single argument
     * (the value from the iterable) and return the transformed version.
     *
     * @return iterable<int|string,T> The transformed values.
     */
    public static function transform(iterable $values, callable $transform): iterable
    {
        foreach ($values as $key => $value) {
            yield $key => $transform($value);
        }
    }

    /** Cache an iterable in an array, and yield a fresh iterable with the same values. */
    public static function cache(iterable $values, array & $cache): iterable
    {
        $cache = [];

        foreach ($values as $key => $value) {
            $cache[$key] = $value;
        }

        yield from $cache;
    }
}
