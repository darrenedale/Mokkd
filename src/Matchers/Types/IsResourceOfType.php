<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/**
 * Matcher that requires the test value to be an open resource of a specified type.
 *
 * Closed resources lose their type information, so it's not possible to match a closed resource of a specified type.
 */
class IsResourceOfType implements MatcherContract
{
    private string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function matches(mixed $actual): bool
    {
        return is_resource($actual) && $this->type() === get_resource_type($actual);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(resource) {{$this->type()}}";
    }
}
