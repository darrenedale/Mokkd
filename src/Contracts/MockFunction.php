<?php

declare(strict_types=1);

namespace Mokkd\Contracts;

interface MockFunction
{
    /** The name of the mocked function. */
    public function name(): string;

    public function expects(mixed ...$args): self;

    public function once(): self;

    public function twice(): self;

    public function times(int $times): self;

    public function never(): self;

    public function returning(mixed $value): self;

    public function returningFrom(array $values): self;

    public function returningMappedValueFrom(array $values): self;

    public function returningUsing(callable $fn): self;

    /**
     * When not blocking, if no matching expectation is found for a call, the call is forwarded to the original
     * function.
     */
    public function withoutBlocking(): self;

    /**
     * When consuming, a mock function "uses up" its expectations as they are matched rather than continuing to match
     * them. When used along with non-blocking mocks, you can precisely control how many times your mock is called
     * before it "expires" and reverts to the original function.
     */
    public function consuming(): self;

    /** Remove the mock. */
    public function uninstall(): void;

    /** @return Expectation[] */
    public function expectations(): array;
}
