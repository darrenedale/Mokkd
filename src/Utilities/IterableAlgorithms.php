<?php

/*
 * Copyright 2025 Darren Edale
 *
 * This file is part of the Mokkd package.
 *
 * Mokkd is free software: you can redistribute it and/or modify
 * it under the terms of the Apache License v2.0.
 *
 * Mokkd is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * Apache License for more details.
 *
 * You should have received a copy of the Apache License v2.0
 * along with Mokkd. If not, see <http://www.apache.org/licenses/>.
 */

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
     * Check that at least one value in an iterable satisfies a predicate.
     *
     * @return true if the predicate returns true for at least one of the iterable's values, false otherwise.
     */
    public static function any(iterable $values, callable $predicate): bool
    {
        foreach ($values as $value) {
            if ($predicate($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check that all values in an iterable fail to satisfy a predicate.
     *
     * @return true if the predicate returns false for all values, false otherwise.
     */
    public static function none(iterable $values, callable $predicate): bool
    {
        foreach ($values as $value) {
            if ($predicate($value)) {
                return false;
            }
        }

        return true;
    }

    /** Yield all the values in an iterable, discarding the keys. */
    public static function values(iterable $values): iterable
    {
        foreach ($values as $value) {
            yield $value;
        }
    }

    /**
     * @template T
     * @template U
     *
     * Transform the values of an iterable using a mutator function.
     *
     * Iterable keys are preserved.
     *
     * @param iterable<int|string,T> $values The values to transform.
     * @param callable $transform The callable to use to transform each value. Required to accept a single argument
     * (the value from the iterable) and return the transformed version.
     *
     * @return iterable<int|string,U> The transformed values.
     */
    public static function transform(iterable $values, callable $transform): iterable
    {
        foreach ($values as $key => $value) {
            yield $key => $transform($value);
        }
    }

    /**
     * @template T
     *
     * Reduce an iterable to a single value using successive calls to a callable.
     *
     * The callable receives the result of the previous call to the reducing function (or the initial value for the
     * first call) - the carry - the value from the iterable, and the key of the value from the iterable, and returns
     * the carry updated with the value and/or key provided. The result of the last call to the reducing function is
     * returned.
     *
     * @param iterable<string|int,mixed> $values The values to reduce
     * @param callable(T,mixed,string|int): T $reduce The function to do the reduction.
     * @param T $initial The initial value to feed to the reducing function.
     *
     * @return T The values reduced to a single value.
     */
    public static function reduce(iterable $values, callable $reduce, mixed $initial): mixed
    {
        foreach ($values as $key => $value) {
            $initial = $reduce($initial, $value, $key);
        }

        return $initial;
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
