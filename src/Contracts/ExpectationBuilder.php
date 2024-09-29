<?php

declare(strict_types=1);

namespace Mokkd\Contracts;

/**
 * Contract for classes that fluently build expectations for mock functions.
 */
interface ExpectationBuilder
{
    /** Add an expectation. */
    public function expects(mixed ...$args): self;

    /** Set the expectation to be matched exactly once. */
    public function once(): self;

    /** Set the expectation to be matched exactly twice. */
    public function twice(): self;

    /**
     * Set the expectation to be matched exactly a specified number of times.
     *
     * @param int $times How many times should the expectation be matched?
     */
    public function times(int $times): self;

    /** Set the expectation to expect zero matches. */
    public function never(): self;

    /** Set the value that the function returns when the expectation matches. */
    public function returning(mixed $value): self;

    /** Set the expectations to return sequential values from an array when it is matched. */
    public function returningFrom(array $values): self;

    /** Set the expectation to return a value mapped from an associative array when the expectation matches. */
    public function returningMappedValueFrom(array $map, int|KeyMapper $keyMapper): self;

    /**
     * Set the expectation to return a value provided by a callable.
     *
     * When the expectation matches, the callable will be invoked with the function call's arguments and its return
     * value will be forwarded as the return value from the function call..
     */
    public function returningUsing(callable $fn): self;

    /** When not blocking, if no matching expectation is found for a call it is forwarded to the original function. */
    public function withoutBlocking(): self;

    /**
     * When blocking, if no matching expectation is found for a call, an ExpectationException is thrown.
     *
     * This is the default state.
     */
    public function blocking(): self;

    /**
     * Matched expectations won't be "used up", but will continue to match appropriate calls beyond their expected call
     * counts.
     *
     * This is the default state of a mock.
     */
    public function withoutConsuming(): self;

    /**
     * When consuming, a mock function "uses up" its expectations as they are matched rather than continuing to match
     * them.
     *
     *  This is useful when you don't necessarily want to test expectations against a function, but you need to control
     *  its return values as part of your test fixture. So if you need time() to return a fixed value for the first
     *  three calls, then to revert to its natural value, you'd set up the mock like this:
     *
     *  ```php
     *  $timeMock = Mokkd\Core::func("time")
     *      ->times(3)
     *      ->consuming()
     *      ->withoutBlocking()
     *      ->returning(12345);
     *  ```
     *
     *  The first three calls to time() will match the mock's expectation, which will then be considered "used up", and
     *  since the mock isn't blocking, subsequent calls will be forwarded to the actual time() function rather than
     *  using the mock.
     */
    public function consuming(): self;
}
