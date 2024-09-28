<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/**
 * Matcher that requires a non-object or an object not of a given class.
 */
class IsNotInstanceOf implements MatcherContract
{
    /** @var class-string The class the test value must not be an instance of. */
    private string $className;

    /** @param class-string $className */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function matches(mixed $actual): bool
    {
        return !is_a($actual, $this->className, false);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(!{$this->className}) {any}";
    }
}
