<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/**
 * Matcher that requires the test value to be an object not of a given class.
 */
class IsObjectNotInstanceOf implements MatcherContract
{
    /** @var class-string  */
    private string $className;

    /** @param class-string $className */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function matches(mixed $actual): bool
    {
        return is_object($actual) && !is_a($actual, $this->className, false);
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(object && !{$this->className}) {any}";
    }
}
