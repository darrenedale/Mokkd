<?php

declare(strict_types=1);

namespace Mokkd\Contracts;

interface MockFunction
{
    public function expects(mixed ...$args): self;

    public function once(): self;

    public function twice(): self;

    public function times(int $times): self;

    public function never(): self;

    public function returning(mixed $value): self;

    public function returningFrom(array $values): self;

    public function returningMappedValueFrom(array $values): self;

    public function returningUsing(callable $fn): self;

    public function remove(): void;
}
