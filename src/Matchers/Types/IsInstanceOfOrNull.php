<?php

declare(strict_types=1);

namespace Mokkd\Matchers\Types;

use Mokkd\Contracts\Matcher as MatcherContract;
use Mokkd\Contracts\Serialiser as SerialiserContract;
use Mokkd\Matchers\Composite\MatchesAnyOf;

/**
 * Matcher that requires the test value to be an object of a given class or null.
 */
class IsInstanceOfOrNull extends MatchesAnyOf
{
    private string $className;

    public function __construct(string $className)
    {
        parent::__construct(new IsNull(), new IsInstanceOf($className));
        $this->className = $className;
    }

    public function describe(SerialiserContract $serialiser): string
    {
        return "(?{$this->className}) {any}";
    }
}
