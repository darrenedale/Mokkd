<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;

/** Matcher that requires an object of a given class or null. */
class IsInstanceOfOrNull implements MatcherContract
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
        return null === $actual || $actual instanceof $this->className;
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?{$this->className}) {any}";
    }
}
