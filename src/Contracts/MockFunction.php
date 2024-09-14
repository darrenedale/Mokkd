<?php

declare(strict_types=1);

namespace Mokkd\Contracts;

/**
 * Contract for mocked functions.
 */
interface MockFunction
{
    /** The name of the mocked function. */
    public function functionName(): string;

    /**
     * Install the mock.
     *
     * If you're using the built-in implementation you don't need to call this directly, Mokkd will do it automatically.
     */
    public function install(): void;

    /**
     * Remove the mock.
     *
     * If you're using the built-in implementation you don't need to call this directly - Mokkd will do this when your
     * test is complete and you call Mokkd::close().
     */
    public function uninstall(): void;

    /** @return Expectation[] */
    public function expectations(): array;

    /**
     * Verify all the mock's expectations.
     *
     * If any expectation is not satisfied, an ExpectationException is thrown.
     */
    public function verifyExpectations(): void;
}
